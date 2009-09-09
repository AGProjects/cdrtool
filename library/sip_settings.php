<?

/*
      Copyright (c) 2007-2008 AG Projects
      http://ag-projects.com
      Author Adrian Georgescu

      This library provide the functions for managing properties
      of SIP accounts retrieved from NGNPro

*/

class SipSettings {

    var $soapTimeout               = 5;
    var $login_type                = 'subscriber';
    var $soapClassSipPort          = 'WebService_NGNPro_SipPort';
    var $soapClassEnumPort         = 'WebService_NGNPro_EnumPort';
    var $soapClassRatingPort       = 'WebService_NGNPro_RatingPort';
    var $soapClassVoicemailPort    = 'WebService_NGNPro_VoicemailPort';
    var $soapClassCustomerPort     = 'WebService_NGNPro_CustomerPort';
    var $soapClassPresencePort     = 'WebService_SoapSIMPLEProxy_PresencePort';
    var $showSoapConnectionInfo    = false;
	var $store_clear_text_passwords=true;

    // these variables can be overwritten per soap engine
    // and subsequently by reseller properties
    // (in ngnpro_soap_engines.inc)

    var $templates_path            = './templates';
    var $support_company           = "Example company";
    var $cdrtool_address           = "https://cdrtool.example.com/CDRTool";
    var $support_web               = "https://www.example.com/help";
    var $support_email             = "Support <support@example.com>";
    var $sip_settings_page         = "https://cdrtool.example.com/sip_settings.phtml";
    var $xcap_root                 = "https://cdrtool.example.com/xcap-root";
    var $pstn_access               = false;
    var $pstn_changes_allowed      = false;
    var $prepaid_changes_allowed   = false;
    var $sip_proxy                 = "proxy.example.com";
    var $voicemail_server          = "vm.example.com";
    var $absolute_voicemail_uri    = false; // use <voice-mailbox>
    var $enable_thor               = false;
    var $sip_accounts_lite         = false;  // show basic settings by default, and advanced if property advanced is set to 1
    var $currency                  = "&euro";
    // access numbers

    var $voicemail_access_number        = "*70";
    var $FUNC_access_number             = "*21*";
    var $FNOL_access_number             = "*22*";
    var $FNOA_access_number             = "*23*";
    var $FBUS_access_number             = "*23*";
    var $change_privacy_access_number   = "*67";
    var $check_privacy_access_number    = "*68";
    var $reject_anonymous_access_number = "*69";

    // end variables

    var $tab                       = "settings";
    var $phonebook_img             = "<img src=images/pb.gif border=0>";
    var $call_img                  = "<img src=images/call.gif border=0>";
    var $delete_img                = "<img src=images/del_pb.gif border=0>";

    var $ForwardingTargetTypes       = array("Voicemail","Other","Mobile","Tel","ENUM","Disabled");

    var $groups                    = array();

    var $WEBdictionary             = array(
                                           'mailto',
                                           'free-pstn',
                                           'blocked',
                                           'sip_password',
                                           'first_name',
                                           'last_name',
                                           'quota',
                                           'language',
                                           'quota_reset',
                                           'voicemail',
                                           'anonymous',
                                           'advanced',
                                           'rpid',
                                           'timezone',
                                           'accept',
                                           'accept_temporary_group',
                                           'accept_temporary_remain',
                                           'web_timestamp',
                                           'acceptDailyStartTime',
                                           'acceptDailyStopTime',
                                           'acceptDailyGroup',
                                           'quickdial',
                                           'delete_voicemail',
                                           'voicemail_password',
                                           'region',
                                           'timeout',
                                           'owner',
                                           'mobile_number',
                                           'extra_groups'
                                           );

    var $presenceStatuses   = array('allow','deny','confirm');
    var $presenceActivities = array('busy'      => 'buddy_busy.jpg',
                                    'open'      => 'buddy_online.jpg',
                                    'available' => 'buddy_online.jpg',
                                    'idle'      => 'buddy_idle.jpg',
                                    'away'      => 'buddy_away.jpg'
                                    );

	var $disable_extra_groups=false;

    var $timeoutEls=array(
             "5" =>"5 s",
             "10"=>"10 s",
             "15"=>"15 s",
             "20"=>"20 s",
             "25"=>"25 s",
             "30"=>"30 s",
             "35"=>"35 s",
             "40"=>"40 s",
             "45"=>"45 s",
             "50"=>"50 s",
             "55"=>"55 s",
             "60"=>"60 s"
             );

    var $prepaid             = 0;
    var $emergencyRegions    = array();
    var $FNOA_timeoutDefault = 35;
    var $exportFilename      = "export.txt";
    var $presenceRules       = array();
    var $enums               = array();
    var $barringPrefixes     = array();
    var $presenceWatchers    = array();
    var $SipUAImagesPath     = "images";
    var $SipUAImagesFile     = "phone_images.php";
    var $balance_history     = array();

    function SipSettings($account,$loginCredentials=array(),$soapEngines=array()) {
        $this->soapEngines        = $soapEngines;

        $debug=sprintf("<font color=blue><p><b>Initialize class %s(%s)</b></font>",get_class($this),$account);
        dprint($debug);
        dprint_r($loginCredentials);

        $this->loginCredentials = &$loginCredentials;

        if ($loginCredentials['login_type']) {
            $this->login_type = $loginCredentials['login_type'];
        } else {
            $this->login_type = 'subscriber';
        }

        if ($this->login_type == "admin") {
            if ($_REQUEST['reseller']) {
                $this->reseller = $_REQUEST['reseller'];
            } else {
                $this->reseller = $loginCredentials['reseller'];
            }
            if ($_REQUEST['customer']) {
                $this->customer = $_REQUEST['customer'];
            } else {
                $this->customer = $loginCredentials['customer'];
            }

        } else {
            $this->reseller           = $loginCredentials['reseller'];
            $this->customer           = $loginCredentials['customer'];
        }

        if (strlen($loginCredentials['sip_engine'])) {
            $this->sip_engine=$loginCredentials['sip_engine'];
        }

        //dprint("Using engine: $this->sip_engine");

        $this->settingsPage       = $_SERVER['PHP_SELF'];
        $this->tab                = $_REQUEST['tab'];

        $this->initSoapClient();

        $this->getAccount($account);

        if ($this->login_type == "admin") {
            $this->pageURL=$this->settingsPage."?account=$this->account&adminonly=1&reseller=$this->reseller&sip_engine=$this->sip_engine";
            $this->hiddenElements="
            <input type=hidden name=account value=\"$this->account\">
            <input type=hidden name=reseller value=$this->reseller>
            <input type=hidden name=sip_engine value=$this->sip_engine>
            <input type=hidden name=adminonly value=1>
            ";

        } else if ($this->login_type == "reseller") {

            $this->pageURL=$this->settingsPage."?account=$this->account&reseller=$this->reseller&sip_engine=$this->sip_engine";
            $this->hiddenElements="
            <input type=hidden name=account value=\"$this->account\">
            <input type=hidden name=reseller value=$this->reseller>
            <input type=hidden name=sip_engine value=$this->sip_engine>
            ";

        } else if ($this->login_type == "customer") {

            $this->pageURL=$this->settingsPage."?account=$this->account&reseller=$this->reseller&sip_engine=$this->sip_engine";
            $this->hiddenElements="
            <input type=hidden name=account value=\"$this->account\">
            <input type=hidden name=reseller value=$this->reseller>
            <input type=hidden name=sip_engine value=$this->sip_engine>
            ";

        } else {
        	$this->pageURL=$this->settingsPage;
			if (!$_SERVER[SSL_CLIENT_CERT]) {
            	$this->pageURL.="?account=$this->account";
            } else {
            	$this->pageURL.="?1=1";
            }
            $this->hiddenElements="
            <input type=hidden name=account value=\"$this->account\">
            <input type=hidden name=sip_engine value=$this->sip_engine>
            ";
        }

        $this->setLanguage();

        if (!$this->username) {
            dprint ("Record not found.");
            return false;
        }

        $this->availableGroups['blocked']  = array("Group"=>"blocked",
                                        "WEBName" =>sprintf(_("Status")),
                                        "SubscriberMayEditIt"=>0,
                                        "SubscriberMaySeeIt"=>1
                                        );

        $this->getResellerSettings();
        $this->getCustomerSettings();

        if ($this->Preferences['advanced']) {

            if ($this->change_privacy_access_number) {
                $_comment=sprintf(_("Dial %s to change"),$this->change_privacy_access_number);
            } else {
                $_comment='';
            }
            $this->availableGroups['anonymous']=array("Group"=>"anonymous",
                                        "WEBName" =>sprintf (_("Privacy")),
                                        "WEBComment"=>$_comment,
                                        "SubscriberMaySeeIt"=>1,
                                        "SubscriberMayEditIt"=>1
                                        );
            if ($this->change_privacy_access_number) {
                $_comment=sprintf(_("Dial %s to change"),$this->reject_anonymous_access_number);
            } else {
                $_comment='';
            }

            $this->availableGroups['anonymous-reject']=array("Group"=>$this->anonymous-reject,
                                        "WEBName" =>sprintf (_("Reject anonymous")),
                                        "WEBComment"=>$_comment,
                                        "SubscriberMaySeeIt"=>1,
                                        "SubscriberMayEditIt"=>1
                                        );

        }

        $this->pstnChangesAllowed();
        $this->prepaidChangesAllowed();

        $this->tabs=array('summary'=>_('Info'),
                          'settings'=>_('Settings'),
                          'locations'=>_('Devices'),
                          'calls'=>_('Calls')
                          );

        $this->acceptDailyProfiles=array('127' => _('Every day'),
                                       '31'  => _('Weekday'),
                                       '96'  => _('Weekend'),
                                       '1'   => _('Monday'),
                                       '2'   => _('Tuesday'),
                                       '4'   => _('Wednesday'),
                                       '8'   => _('Thursday'),
                                       '16'  => _('Friday'),
                                       '32'  => _('Saturday'),
                                       '64'  => _('Sunday')
                                       );

        $this->PhonebookGroups=array(
        "vip"       =>sprintf(_("VIP")),
        "business"  =>sprintf(_("Business")),
        "coworkers" =>sprintf(_("Coworkers")),
        "friends"   =>sprintf(_("Friends")),
        "family"    =>sprintf(_("Family"))
        );

        $this->CallPrefDictionary=array(
        "FUNC"=>sprintf(_("All calls")),
        "FNOL"=>sprintf(_("If Not-Online")),
        "FBUS"=>sprintf(_("If Busy")),
        "FNOA"=>sprintf(_("If No-Answer")),
        "FUNV"=>sprintf(_("If Unavailable"))
        );

        $this->CallPrefDictionaryUNV=array(
        "FUNV"=>sprintf(_("If Unavailable"))
        );

        $this->VoicemailCallPrefDictionary=array(
        "FNOL"=>sprintf(_("If Not-Online")),
        "FBUS"=>sprintf(_("If Busy")),
        "FNOA"=>sprintf(_("If No-Answer")),
        "FUNV"=>sprintf(_("If Unavailable"))
        );

        if ($this->Preferences['advanced']) {
            // Advanced accounts has access to call control and Phonebook
            $this->tabs['phonebook']=_("Contacts");
            $this->tabs['accept'] =_("Accept");
            $this->tabs['reject'] =_("Reject");

            if (in_array("free-pstn",$this->groups)) {
                $this->tabs['barring']=_("Barring");
            }
        }

        if ($this->presence_engine) {
            $this->tabs['presence']=_("Presence");
        }

        $this->access_numbers=array("FUNC"=>$this->FUNC_access_number,
                                    "FNOA"=>$this->FNOA_access_number,
                                    "FBUS"=>$this->FBUS_access_number,
                                    "FNOL"=>$this->FNOL_access_number
                                    );
        if ($this->prepaid) {
            $this->tabs['prepaid']=_("Credit");
        }

    }

    function initSoapClient() {
        dprint("initSoapClient()");

        require_once('SOAP/Client.php');
        require_once("ngnpro_soap_library.php");

        // Sip, Voicemail and Customer ports share same login
        $this->SOAPurl=$this->soapEngines[$this->sip_engine]['url'];

        $this->SOAPversion=$this->soapEngines[$this->sip_engine]['version'];

        if (strlen($this->loginCredentials['soapUsername'])) {
            $this->SOAPlogin = array(
                                   "username"    => $this->loginCredentials['soapUsername'],
                                   "password"    => $this->loginCredentials['soapPassword'],
                                   "admin"       => false
                                   );
            $this->soapUsername = $this->loginCredentials['soapUsername'];

        } else {
            $this->SOAPlogin = array(
                                   "username"    => $this->soapEngines[$this->sip_engine]['username'],
                                   "password"    => $this->soapEngines[$this->sip_engine]['password'],
                                   "admin"       => true,
                                   "impersonate" => intval($this->reseller)
                                   );
            $this->soapUsername = $this->soapEngines[$this->sip_engine]['username'];
        }

        $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

        //dprint_r($this->SoapAuth);

        // Presence
        $this->SOAPurlPresence=$this->soapEngines[$this->presence_engine]['url'];

        if (strlen($this->loginCredentials['customer_engine'])) {
            $this->customer_engine=$this->loginCredentials['customer_engine'];
        } else if (strlen($this->soapEngines[$this->sip_engine]['customer_engine'])) {
            $this->customer_engine=$this->soapEngines[$this->sip_engine]['customer_engine'];
        } else {
            $this->customer_engine=$this->sip_engine;
        }

        if (strlen($this->loginCredentials['presence_engine'])) {
            $this->presence_engine=$this->loginCredentials['presence_engine'];
        } else if (strlen($this->soapEngines[$this->sip_engine]['presence_engine'])) {
            $this->presence_engine=$this->soapEngines[$this->sip_engine]['presence_engine'];
        }

        if (strlen($this->loginCredentials['voicemail_engine'])) {
            $this->voicemail_engine=$this->loginCredentials['voicemail_engine'];
        } else if (strlen($this->soapEngines[$this->sip_engine]['voicemail_engine'])) {
            $this->voicemail_engine=$this->soapEngines[$this->sip_engine]['voicemail_engine'];
        } else {
            $this->voicemail_engine=$this->sip_engine;
        }

        if (strlen($this->loginCredentials['enum_engine'])) {
            $this->enum_engine=$this->loginCredentials['enum_engine'];
        } else if (strlen($this->soapEngines[$this->sip_engine]['enum_engine'])) {
            $this->enum_engine=$this->soapEngines[$this->sip_engine]['enum_engine'];
        } else {
            $this->enum_engine=$this->sip_engine;
        }

        if (strlen($this->loginCredentials['rating_engine'])) {
            $this->rating_engine=$this->loginCredentials['rating_engine'];
        } else if (strlen($this->soapEngines[$this->sip_engine]['rating_engine'])) {
            $this->rating_engine=$this->soapEngines[$this->sip_engine]['rating_engine'];
        } else {
            $this->rating_engine=$this->sip_engine;
        }

        // overwrite default settings
        if (strlen($this->soapEngines[$this->sip_engine]['sip_proxy'])) {
            $this->sip_proxy        = $this->soapEngines[$this->sip_engine]['sip_proxy'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['support_company'])) {
            $this->support_company  = $this->soapEngines[$this->sip_engine]['support_company'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['support_web'])) {
            $this->support_web  = $this->soapEngines[$this->sip_engine]['support_web'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['support_email'])) {
            $this->support_email    = $this->soapEngines[$this->sip_engine]['support_email'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['sip_settings_page'])) {
            $this->sip_settings_page = $this->soapEngines[$this->sip_engine]['sip_settings_page'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['xcap_root'])) {
            $this->xcap_root       = $this->soapEngines[$this->sip_engine]['xcap_root'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['cdrtool_address'])) {
            $this->cdrtool_address  = $this->soapEngines[$this->sip_engine]['cdrtool_address'];
        }

        if ($this->soapEngines[$this->sip_engine]['pstn_access']) {
            $this->pstn_access     = $this->soapEngines[$this->sip_engine]['pstn_access'];
        }

        if ($this->soapEngines[$this->sip_engine]['voicemail_server']) {
            $this->voicemail_server = $this->soapEngines[$this->sip_engine]['voicemail_server'];
        }

        if ($this->soapEngines[$this->sip_engine]['currency']) {
            $this->currency = $this->soapEngines[$this->sip_engine]['currency'];
        }

        if ($this->soapEngines[$this->sip_engine]['voicemail_access_number']) {
            $this->voicemail_access_number = $this->soapEngines[$this->sip_engine]['voicemail_access_number'];
        }

        if ($this->soapEngines[$this->sip_engine]['FUNC_access_number']) {
            $this->FUNC_access_number = $this->soapEngines[$this->sip_engine]['FUNC_access_number'];
        }

        if ($this->soapEngines[$this->sip_engine]['FNOA_access_number']) {
            $this->FNOA_access_number = $this->soapEngines[$this->sip_engine]['FNOA_access_number'];
        }

        if ($this->soapEngines[$this->sip_engine]['FBUS_access_number']) {
            $this->FBUS_access_number = $this->soapEngines[$this->sip_engine]['FBUS_access_number'];
        }

        if (isset($this->soapEngines[$this->sip_engine]['absolute_voicemail_uri'])) {
            $this->absolute_voicemail_uri = $this->soapEngines[$this->sip_engine]['absolute_voicemail_uri'];
        }

        if (isset($this->soapEngines[$this->sip_engine]['enable_thor'])) {
            $this->enable_thor=$this->soapEngines[$this->sip_engine]['enable_thor'];
        }

        if (isset($this->soapEngines[$this->sip_engine]['sip_accounts_lite'])) {
            $this->sip_accounts_lite=$this->soapEngines[$this->sip_engine]['sip_accounts_lite'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['timeout'])) {
            $this->soapTimeout=intval($this->soapEngines[$this->sip_engine]['timeout']);
        }

        if (strlen($this->soapEngines[$this->sip_engine]['store_clear_text_passwords'])) {
            $this->store_clear_text_passwords=$this->soapEngines[$this->sip_engine]['store_clear_text_passwords'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['disable_extra_groups'])) {
            $this->disable_extra_groups=$this->soapEngines[$this->sip_engine]['disable_extra_groups'];
        }

        if ($this->loginCredentials['templates_path']) {
            $this->templates_path   = $this->loginCredentials['templates_path'];
        } else if ($this->soapEngines[$this->sip_engine]['templates_path']) {
            $this->templates_path   = $this->soapEngines[$this->sip_engine]['templates_path'];
        }

        // Instantiate the SOAP clients

        // sip
        $this->SipPort       = new $this->soapClassSipPort($this->SOAPurl);

        $this->SipPort->_options['timeout'] = $this->soapTimeout;

        $this->SipPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->SipPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        if ($this->showSoapConnectionInfo)  {
            printf ("<p>%s at <a href=%swsdl target=wsdl>%s</a> as %s ",$this->soapClassSipPort,$this->SOAPurl,$this->SOAPurl,$this->soapUsername);
        }

        // voicemail
        $this->SOAPurlVoicemail = $this->soapEngines[$this->voicemail_engine]['url'];
        $this->SOAPloginVoicemail = array(
                               "username"    => $this->soapEngines[$this->voicemail_engine]['username'],
                               "password"    => $this->soapEngines[$this->voicemail_engine]['password'],
                               "admin"       => true,
                               "impersonate" => intval($this->reseller)
                               );

        $this->SoapAuthVoicemail = array('auth', $this->SOAPloginVoicemail , 'urn:AGProjects:NGNPro', 0, '');
        $this->VoicemailPort = new $this->soapClassVoicemailPort($this->SOAPurlVoicemail);

        if (strlen($this->soapEngines[$this->voicemail_engine]['timeout'])) {
            $this->VoicemailPort->_options['timeout'] = intval($this->soapEngines[$this->voicemail_engine]['timeout']);
        } else {
            $this->VoicemailPort->_options['timeout'] = $this->soapTimeout;
        }

        $this->VoicemailPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->VoicemailPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        if ($this->showSoapConnectionInfo && $this->SOAPurlVoicemail != $this->SOAPurl)  {
            printf ("<br>%s at <a href=%swsdl target=wsdl>%s</a> as %s ",$this->soapClassVoicemailPort,$this->SOAPurlVoicemail,$this->SOAPurlVoicemail,$this->soapEngines[$this->voicemail_engine]['username']);
        }

        // enum
        $this->SOAPurlEnum = $this->soapEngines[$this->enum_engine]['url'];
        $this->SOAPloginEnum = array(
                               "username"    => $this->soapEngines[$this->enum_engine]['username'],
                               "password"    => $this->soapEngines[$this->enum_engine]['password'],
                               "admin"       => true,
                               "impersonate" => intval($this->reseller)
                               );

        $this->SoapAuthEnum = array('auth', $this->SOAPloginEnum , 'urn:AGProjects:NGNPro', 0, '');
        $this->EnumPort = new $this->soapClassEnumPort($this->SOAPurlEnum);

        if (strlen($this->soapEngines[$this->enum_engine]['timeout'])) {
            $this->EnumPort->_options['timeout'] = intval($this->soapEngines[$this->enum_engine]['timeout']);
        } else {
            $this->EnumPort->_options['timeout'] = $this->soapTimeout;
        }

        $this->EnumPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->EnumPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        if ($this->showSoapConnectionInfo && $this->SOAPurlEnum != $this->SOAPurl)  {
            printf ("<br>%s at <a href=%swsdl target=wsdl>%s</a> as %s ",$this->soapClassEnumPort,$this->SOAPurlEnum,$this->SOAPurlEnum,$this->soapEngines[$this->enum_engine]['username']);
        }

        // rating
        $this->SOAPurlRating = $this->soapEngines[$this->rating_engine]['url'];
        $this->SOAPloginRating = array(
                               "username"    => $this->soapEngines[$this->rating_engine]['username'],
                               "password"    => $this->soapEngines[$this->rating_engine]['password'],
                               "admin"       => true,
                               "impersonate" => intval($this->reseller)
                               );

        $this->SoapAuthRating = array('auth', $this->SOAPloginRating , 'urn:AGProjects:NGNPro', 0, '');
        $this->RatingPort = new $this->soapClassRatingPort($this->SOAPurlRating);

        if (strlen($this->soapEngines[$this->rating_engine]['timeout'])) {
            $this->RatingPort->_options['timeout'] = intval($this->soapEngines[$this->rating_engine]['timeout']);
        } else {
            $this->RatingPort->_options['timeout'] = $this->soapTimeout;
        }

        $this->RatingPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->RatingPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        if ($this->showSoapConnectionInfo  && $this->SOAPurlRating != $this->SOAPurl)  {
            printf ("<br>%s at <a href=%swsdl target=wsdl>%s</a> as %s ",$this->soapClassRatingPort,$this->SOAPurlRating,$this->SOAPurlRating,$this->soapEngines[$this->rating_engine]['username']);
        }

        // customer
        $this->SOAPurlCustomer = $this->soapEngines[$this->customer_engine]['url'];
        $this->SOAPloginCustomer = array(
                               "username"    => $this->soapEngines[$this->customer_engine]['username'],
                               "password"    => $this->soapEngines[$this->customer_engine]['password'],
                               "admin"       => true
                               );

        $this->SoapAuthCustomer = array('auth', $this->SOAPloginCustomer , 'urn:AGProjects:NGNPro', 0, '');
        $this->CustomerPort  = new $this->soapClassCustomerPort($this->SOAPurlCustomer);

        if (strlen($this->soapEngines[$this->customer_engine]['timeout'])) {
            $this->CustomerPort->_options['timeout'] = intval($this->soapEngines[$this->customer_engine]['timeout']);
        } else {
            $this->CustomerPort->_options['timeout'] = $this->soapTimeout;
        }

        $this->CustomerPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
        $this->CustomerPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);

        if ($this->showSoapConnectionInfo && $this->SOAPurlCustomer != $this->SOAPurl)  {
            printf ("<br>%s at <a href=%swsdl target=wsdl>%s</a> as %s ",$this->soapClassCustomerPort,$this->SOAPurlCustomer,$this->SOAPurlCustomer,$this->soapEngines[$this->customer_engine]['username']);
        }

        // presence
        if ($this->presence_engine) {
            $this->SOAPurlPresence = $this->soapEngines[$this->presence_engine]['url'];
            $this->PresencePort    = new $this->soapClassPresencePort($this->SOAPurlPresence);

            if (strlen($this->soapEngines[$this->presence_engine]['timeout'])) {
                $this->PresencePort->_options['timeout'] = intval($this->soapEngines[$this->presence_engine]['timeout']);
            } else {
                $this->PresencePort->_options['timeout'] = $this->soapTimeout;
            }

            $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
            $this->PresencePort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
            if ($this->showSoapConnectionInfo)  {
                printf ("<br>%s at <a href=%swsdl target=wsdl>%s</a> ",$this->soapClassPresencePort,$this->SOAPurlPresence,$this->SOAPurlPresence);
            }
        }

    }

    function getAccount($account) {
        dprint("getAccount($account, engine=$this->sip_engine)");

        list($username,$domain)=explode("@",trim($account));

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getAccount(array("username" =>$username,"domain"   =>$domain));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        //print_r($result);

        $this->owner     = $result->owner;

        if (!is_array($result->properties))   $result->properties=array();
        if (!is_array($result->groups))       $result->groups=array();

        foreach ($result->properties as $_property) {
            $this->Preferences[$_property->name]=$_property->value;
        }

        dprint_r($this->Preferences);
        if (!$this->sip_accounts_lite) {
            $this->Preferences['advanced']=1;
        }

        if (!$this->Preferences['language']) {
            $this->Preferences['language'] ='en';
        }

        $this->username  = $result->id->username;
        $this->domain    = $result->id->domain;
        $this->password  = $result->password;
        $this->firstName = $result->firstName;
        $this->lastName  = $result->lastName;
        $this->rpid      = $result->rpid;
        $this->owner     = $result->owner;
        $this->timezone  = $result->timezone;
        $this->email     = $result->email;
        $this->groups    = $result->groups;
        $this->createDate= $result->createDate;

        if ($this->SOAPversion > 1) {
            $this->quickdial = $result->quickdialPrefix;
            $this->timeout   = intval($result->timeout);
        } else {
            $this->quickdial = $result->quickdial;
            $this->timeout   = intval($result->answerTimeout);
        }
        $this->quota     = $result->quota;
        $this->prepaid   = intval($result->prepaid);
        $this->region    = $result->region;

        $this->account   = $this->username."@".$this->domain;
        $this->fullName  = $this->firstName." ".$this->lastName;
        $this->name      = $this->firstName." ".$this->lastName;

        $this->sipId=array("username" => $this->username,
                           "domain" => $this->domain
                           );

        if (!$this->timeout) {
            $this->timeoutWasNotSet=1;
            $this->timeout=intval($this->FNOA_timeoutDefault);
        }

        $this->getOwnerSettings($this->owner);

        $this->getDomainOwner($this->domain);

        $this->getMobileNumber();

        $this->xcap_root     = rtrim($this->xcap_root,'/')."@".$this->domain."/";

        $this->result    = $result;

    }

    function showAccount() {

        if (!$this->account) {
            print "<tr><td colspan=>";
            printf ("Error: SIP account information cannot be retrieved");
            return 0;
            print "</td></tr>";
        }

        print "
        <SCRIPT>
        var _action = '';
        function checkForm(form) {
            if (_action=='send' && form.mailto.value=='') {
                window.alert('Please fill in the Email address');
                form.mailto.focus();
                return false;
            } else {
                return true;
            }
        }
        function saveHandler(elem) {
            _action = 'save';
        }
        function sendHandler(elem) {
            _action = 'send';
        }
        </SCRIPT>
        ";

        $this->showHeader();

        $this->chapterTableStart();

    	$this->showAboveTabs();
        $this->showTabs();
    	$this->showUnderTabs();

        $this->showTitleBar();

        if (!array_key_exists($this->tab,$this->tabs)) $this->tab="settings";

        if ($this->tab=="summary")   $this->showSummary();
        if ($this->tab=="settings")  $this->showSettings();
        if ($this->tab=="locations") $this->showDeviceLocations();
        if ($this->tab=="calls")     $this->showLastCalls();
        if ($this->tab=="phonebook") $this->showPhonebook();
        if ($this->tab=="prepaid")   $this->showPrepaidForm();
        if ($this->tab=="upgrade")   $this->showUpgrade();
        if ($this->tab=="barring")   $this->showBarringPrefixes();
        if ($this->tab=="reject")    $this->showRejectMembers();
        if ($this->tab=="accept")    $this->showAcceptRules();
        if ($this->tab=="presence")  $this->showPresence();

        $this->showFooter();

    	$this->chapterTableStop();
    }

    function getDomainOwner ($domain='') {
        dprint("getdomainOwner(domain=$domain)");

        if ($this->SOAPversion < 2) return;

        if (!$domain) return;
        // Filter
        $filter=array('domain'    => $domain);

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

        //dprint_r($Query);

        // Call function
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getDomains($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->domains[0]) {
                $this->reseller  = $result->domains[0]->reseller;
                $this->customer  = $result->domains[0]->customer;
            }
        }
    }

    function getMobileNumber() {
        $this->mobile_number='';

        if ($this->Preferences['mobile_number']) {
            $this->mobile_number=$this->Preferences['mobile_number'];
        } else if ($this->SIP['customer']['mobile']) {
            $this->mobile_number=$this->SIP['customer']['mobile'];
        }
    }

    function setLanguage() {
        dprint("setLanguage()");

        if ($this->login_type == 'reseller' || $this->login_type == 'customer') {
            $lang = $this->ResellerLanguage;
        } else if ($this->login_type == 'subscriber') {
            $lang = $this->Preferences['language'];
        } else {
            $lang = "en";
        }

        dprint("Set language to $lang");
        changeLanguage($lang);
    }


    function getOwnerSettings($owner='') {
        dprint("getOwnerSettings($owner, engine=$this->customer_engine)");
        if (!$owner) {
            return false;
        }

        $this->CustomerPort->addHeader($this->SoapAuthCustomer);
        //dprint_r($this->CustomerPort);
        $result     = $this->CustomerPort->getAccount($owner);
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (CustomerPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }
 
        $this->SIP['customer']=array(
                               "firstName"           => $result->firstName,
                               "lastName"            => $result->lastName,
                               "organization"        => $result->organization,
                               "timezone"            => $result->timezone,
                               "tel"                 => $result->tel,
                               "enum"                => $result->enum,
                               "mobile"              => $result->mobile,
                               "fax"                 => $result->fax,
                               "email"               => $result->email,
                               "web"                 => $result->web
                               );
    }

    function getAliases() {
        // Get Aliases
        dprint("getAliases()");

        $this->aliases=array();

        $this->SipPort->addHeader($this->SoapAuth);

        if ($this->SOAPversion > 1) {
            // Filter
            $filter=array('targetUsername' => $this->username,
                          'targetDomain'   => $this->domain
                          );
    
            // Range
            $range=array('start' => 0,
                         'count' => 20
                         );
    
            // Order
            $orderBy = array('attribute' => 'aliasUsername',
                             'direction' => 'ASC'
                             );
    
            // Compose query
            $Query=array('filter'  => $filter,
                            'orderBy' => $orderBy,
                            'range'   => $range
                            );
    
            // Call function
            $result     = $this->SipPort->getAliases($Query);
        } else {
            $result     = $this->SipPort->getAliasesForAccount($this->sipId);
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        //dprint_r($result);
        if ($this->SOAPversion > 1) {
            foreach ($result->aliases as $_alias) {
                $this->aliases[]=$_alias->id->username.'@'.$_alias->id->domain;
            }

        } else {
            foreach ($result as $_alias) {
                if ($_alias->domain == $this->domain) {
                    $this->aliases[]=$_alias->username.'@'.$_alias->domain;
                }
            }
        }
    }

    function getRatingEntityProfiles() {
        dprint("getRatingEntityProfiles()");

        $this->EntityProfiles=array();

        $this->RatingPort->addHeader($this->SoapAuth);
        $entity="subscriber://".$this->username."@".$this->domain;
        $result     = $this->RatingPort->getEntityProfiles($entiry);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (RatingPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        $this->EntityProfiles=$result;
    }

    function setAliases() {
        dprint("setAliases()");

        $aliases_new=$_REQUEST['aliases'];
        $this->getAliases();
        $aliases_old=$this->aliases;

        $addAliases     = array_unique(array_diff($aliases_new,$aliases_old));
        $deleteAliases  = array_unique(array_diff($aliases_old,$aliases_new));

        foreach ($addAliases as $_alias) {
            $_alias=trim(strtolower($_alias));

            if (!preg_match("/^[a-z0-9-_.@]+$/i",$_alias)) continue;

            $els=explode("@",$_alias);

            if (count($els) ==1 ) {
                $_alias_username=$_alias;
                $_alias_domain=$this->domain;
            } else if (count($els) ==2) {
                $_alias_username=$els[0];
                $_alias_domain=$this->domain;
            } else {
                continue ;
            }

            $_aliasObject=array("id"=>array("username"=>strtolower($_alias_username),
                                            "domain"=>strtolower($_alias_domain)
                                            ),
                                "owner"=>intval($this->owner),
                                "target"=>$this->sipId
                                )
                                ;

            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->addAlias($_aliasObject);

            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        }

        foreach ($deleteAliases as $_alias) {
            $_alias=trim($_alias);
            if (!strlen($_alias)) continue;
            $els=explode("@",$_alias);
            if (count($els) ==1 ) {
                $_alias_username=$_alias;
                $_alias_domain=$this->domain;
            } else if (count($els) == 2) {
                $_alias_username=$els[0];
                $_alias_domain=$els[1];
            } else {
                continue ;
            }

            $_aliasObject=array("username"=>$_alias_username,
                                "domain"  =>$_alias_domain
                               );
            dprint_r($_aliasObject);

            dprint("deleteAlias");
 
            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->deleteAlias($_aliasObject);
 
            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        }

        unset($this->aliases);
    }

    function getVoicemail () {
        dprint("getVoicemail(engine=$this->voicemail_engine)");

        $this->VoicemailPort->addHeader($this->SoapAuthVoicemail);
        $result     = $this->VoicemailPort->getAccount($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "2000" && $error_fault->detail->exception->errorcode != "1010") {
                printf ("<p><font color=red>Error (VoicemailPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            } else {
                return true;
            }
        }

        if (!$result->mailbox) {
            dprint ("No voicemail account found");
            return false;
        }

        $this->voicemail['Mailbox']        = $result->mailbox;
        $this->voicemail['Password']       = $result->password;
        $this->voicemail['Name']           = $result->name;
        $this->voicemail['Email']          = $result->email;
        $this->voicemail['Info']           = $result->info;
        $this->voicemail['Options']        = $result->options;
        $this->voicemail['Account']        = $result->mailbox.'@'.$this->voicemail_server;

        // used by template system
        $this->voicemailMailbox            = $result->mailbox;

        //dprint_r($this->voicemail);
        return true;
    }

    function showTitleBar() {

        print "
        <tr>
        <td class=border colspan=2 bgcolor=lightgrey>
        ";

        print "
        <table border=0 width=100% cellpadding=1 cellspacing=1 bgcolor=lightgrey>
        <tr>
        <td align=left>
        ";
        printf (("%s &lt;sip:%s@%s&gt;"),$this->fullName,$this->username,$this->domain);

        print "
        </td>
        <td align=right valign=top>
        ";
    
        if ($this->login_type == 'subscriber' && !$_SERVER[SSL_CLIENT_CERT]) {
            print "<a href=sip_logout.phtml>";
            print _("Logout");
            print "</a>";
        } else {
            if ($this->enable_thor) {
                if ($this->homeNode=getSipThorHomeNode($this->account,$this->sip_proxy)) {
                    printf (" Home node <font color=green>%s</font>",$this->homeNode);
                }
            }
        }
    
        print "
        </td>
        </tr>
        </table>
        ";
        print "
        </td>
        </tr>
        ";

    }

    function getDivertTargets () {
        dprint("getDivertTargets()");

        if (!$this->Preferences['advanced']) {
            return true;
        }

        $this->divertTargets[] = array("name"          => _("No diversion"),
                                       "value"         => "",
                                       "description"   => "Disabled"
                                       );

        if ($this->voicemail['Account']) {
            $vmf=$this->getVoicemailForwarding();
            if (is_array($vmf)) {
                $this->divertTargets[]=$vmf;
            }
        }

        if ($this->owner) {

            if (in_array("free-pstn",$this->groups)) {

                if ($this->SIP['customer']['tel']) {
                    $tel  = preg_replace("/[^\d+]/", "", $this->SIP['customer']['tel']);
                    $tel_enum = str_replace("+", "00", $tel);
                    $telf = $tel_enum . "@" . $this->domain;
                    if (!$seen[$tel_enum] && !in_array($tel_enum,$this->enums)) {
                        $this->divertTargets[]=array("name"  => sprintf (_("Tel %s"),$tel),
                                                      "value" => $telf,
                                                      "description"  => "Tel");
                    }
                    $seen[$tel_enum]++;
                }

                if ($this->SIP['customer']['enum']) {
                    $tel  = preg_replace("/[^\d+]/", "", $this->SIP['customer']['enum']);
                    $tel_enum = str_replace("+", "00", $tel);
                    $telf = $tel_enum . "@" . $this->domain;
                    if (!$seen[$tel_enum] && !in_array($tel_enum,$this->enums)) {
                        $this->divertTargets[]=array("name"  => sprintf (_("Tel %s"),$tel),
                                                      "value" => $telf,
                                                      "description"  => "ENUM");
                    }
                    $seen[$tel_enum]++;
                }

            }

        }

        if ($this->mobile_number) {
            $tel  = preg_replace("/[^\d+]/", "", $this->mobile_number);
            $tel_enum = str_replace("+", "00", $tel);
            $telf = $tel_enum . "@" . $this->domain;
            if (!$seen[$tel_enum] && !in_array($tel_enum,$this->enums)) {
                $this->divertTargets[] = array("name"        => sprintf (_("Mobile %s"),$tel),
                                               "value"       => $telf,
                                               "description" => "Mobile"
                                               );
            }
            $seen[$tel_enum]++;
        }


        $this->divertTargets[]=array("name"  => sprintf (_("Other")),
                                      "value" => "",
                                      "description"  => "Other"
                                      );

        //dprint_r($this->divertTargets);

    }

    function pstnChangesAllowed() {
        dprint("pstnChangesAllowed()");
        if ($this->login_type == 'subscriber') {
            $this->pstn_changes_allowed = false;
            return;
        } else if ($this->login_type == 'admin') {
            $this->pstn_changes_allowed = true;
            return;
        } else if ($this->login_type == 'reseller') {

            // for a reseller we need to check if a subaccount is allowed
            if ($this->loginCredentials['customer'] == $this->loginCredentials['reseller']) {
                dprint("is reseller");
                $this->pstn_changes_allowed = true;
                return;
            } else if ($this->customerImpersonate == $this->loginCredentials['reseller']) {
                dprint("impersonate reseller");
                $this->pstn_changes_allowed = true;
                return;
            }
        } else if ($this->login_type == 'customer') {
            if ($this->customerProperties['pstn_changes']) {
                $this->pstn_changes_allowed = true;
                return;
            }
        }

        $this->pstn_changes_allowed = false;
        return;
    }

    function prepaidChangesAllowed() {
        dprint("prepaidChangesAllowed()");
        if ($this->login_type == 'subscriber') {
            $this->prepaid_changes_allowed = false;
            return;
        } else if ($this->login_type == 'admin') {
            $this->prepaid_changes_allowed = true;
            return;
        } else if ($this->login_type == 'reseller') {

            // for a reseller we need to check if a subaccount is allowed
            if ($this->loginCredentials['customer'] == $this->loginCredentials['reseller']) {
                dprint("is reseller");
                $this->prepaid_changes_allowed = true;
                return;
            } else if ($this->customerImpersonate == $this->loginCredentials['reseller']) {
                dprint("impersonate reseller");
                $this->prepaid_changes_allowed = true;
                return;
            }
        } elseif ($this->login_type == 'customer') {
            if ($this->customerProperties['prepaid_changes']) {
                $this->prepaid_changes_allowed = true;
                return;
            }
        }

        $this->prepaid_changes_allowed = false;
        return;
    }

    function getCustomerSettings () {
        if (!$this->loginCredentials['customer']) return;

        $id=$this->loginCredentials['customer'];
        dprint("getCustomerSettings($id,engine=$this->customer_engine)");

        $this->CustomerPort->addHeader($this->SoapAuthCustomer);
        $result     = $this->CustomerPort->getAccount(intval($this->loginCredentials['customer']));
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (CustomerPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        foreach ($result->properties as $_property) {
            $this->customerProperties[$_property->name]=$_property->value;
        }

        $this->customerImpersonate=$result->impersonate;
        //dprint_r($this->customerProperties);

    }

    function getResellerSettings () {
        dprint("getResellerSettings($this->reseller,engine=$this->customer_engine)");

        $this->logoFile         = $this->getFileTemplate("logo","logo");
        $this->headerFile       = $this->getFileTemplate("header.phtml");
        $this->footerFile       = $this->getFileTemplate("footer.phtml");
        $this->cssFile          = $this->getFileTemplate("main.css");

        if (!$this->reseller) {
            if ($this->pstn_access) {
                $this->availableGroups['free-pstn'] = array("Group"=>"free-pstn",
                                                    "WEBName" =>   sprintf(_("PSTN")),
                                                    "WEBComment"=> sprintf(_("Caller-ID")),
                                                    "SubscriberMayEditIt" => "0",
                                                    "SubscriberMaySeeIt"  => 1
                                                    );
            }

            return true;
        }

        $this->CustomerPort->addHeader($this->SoapAuthCustomer);
        $result     = $this->CustomerPort->getAccount(intval($this->reseller));
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (CustomerPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        foreach ($result->properties as $_property) {
            $this->resellerProperties[$_property->name]=$_property->value;
        }

        $this->resellerProperties['language'] = $result->language;
        $this->resellerProperties['timezone'] = $result->timezone;

        dprint_r($this->resellerProperties);

        // overwrite settings from soap engine
        if ($this->resellerProperties['sip_proxy']) {
            $this->sip_proxy             = $this->resellerProperties['sip_proxy'];
        }

        if ($this->resellerProperties['store_clear_text_passwords']) {
            $this->store_clear_text_passwords = $this->resellerProperties['store_clear_text_passwords'];
        }

        if ($this->resellerProperties['support_company']) {
            $this->support_company    = $this->resellerProperties['support_company'];
        }

        if ($this->resellerProperties['support_web']) {
            $this->support_web     = $this->resellerProperties['support_web'];
        }

        if ($this->resellerProperties['support_email']) {
            $this->support_email         = $this->resellerProperties['support_email'];
        }

        if ($this->resellerProperties['sip_settings_page']) {
            $this->sip_settings_page = $this->resellerProperties['sip_settings_page'];
        }

        if ($this->resellerProperties['xcap_root']) {
            $this->xcap_root      = rtrim($this->resellerProperties['xcap_root'],'/');
              $this->xcap_root     .= "@".$this->domain."/";
        }

        if ($this->resellerProperties['cdrtool_address']) {
            $this->cdrtool_address     = $this->resellerProperties['cdrtool_address'];
        }

        if (isset($this->resellerProperties['voicemail_server'])) {
            $this->voicemail_server     = $this->resellerProperties['voicemail_server'];
        }

        if (isset($this->resellerProperties['voicemail_access_number'])) {
            $this->voicemail_access_number = $this->resellerProperties['voicemail_access_number'];
        }

        if (isset($this->resellerProperties['currency'])) {
            $this->currency = $this->resellerProperties['currency'];
        }

        if (isset($this->resellerProperties['FUNC_access_number'])) {
            $this->FUNC_access_number = $this->resellerProperties['FUNC_access_number'];
        }

        if (isset($this->resellerProperties['FNOA_access_number'])) {
            $this->FNOA_access_number = $this->resellerProperties['FNOA_access_number'];
        }

        if (isset($this->resellerProperties['FBUS_access_number'])) {
            $this->FBUS_access_number = $this->resellerProperties['FBUS_access_number'];
        }

        if (isset($this->resellerProperties['absolute_voicemail_uri'])) {
            $this->absolute_voicemail_uri = $this->resellerProperties['absolute_voicemail_uri'];
        }

        if (isset($this->resellerProperties['pstn_access'])) {
            $this->pstn_access     = $this->resellerProperties['pstn_access'];
        }

        if ($this->pstn_access) {
            $this->availableGroups['free-pstn'] = array("Group"=>"free-pstn",
                                                "WEBName" =>   sprintf(_("PSTN")),
                                                "WEBComment"=> sprintf(_("Caller-ID")),
                                                "SubscriberMayEditIt" => "0",
                                                "SubscriberMaySeeIt"  => 1
                                                );
        }
    }

    function getDiversions() {
        dprint("getDiversions()");
        if (!$this->Preferences['advanced']) {
            return true;
        }

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getCallDiversions($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        //dprint_r($result);

        reset($this->CallPrefDictionary);
        foreach(array_keys($this->CallPrefDictionary) as $condition) {
            $uri=$result->$condition;

            if ($uri == "<voice-mailbox>" && $this->absolute_voicemail_uri) {
                $uri = $this->voicemail['Account'];
            }

            if (preg_match("/^(sip:|sips:)(.*)$/i",$uri,$m)) {
                $uri=$m[2];
            }

            $this->CallPrefDbURI[$condition]=$uri;
        }

        //dprint_r($this->CallPrefDbURI);
    }

    function getDeviceLocations() {
        dprint("getDeviceLocations()");
        require($this->SipUAImagesFile);
        $this->userAgentImages = $userAgentImages;

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getSipDeviceLocations(array($this->sipId));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }  else {
            foreach ($result[0]->locations as $locationStructure) {
                $contact=$locationStructure->address.":".$locationStructure->port;
                if ($locationStructure->publicAddress) {
                    $publicContact=$locationStructure->publicAddress.":".$locationStructure->publicPort;
                } else {
                    $publicContact=$contact;
                }
                $this->locations[]=array("contact"       => $contact,
                                         "publicContact" => $publicContact,
                                         "expires"       => $locationStructure->expires,
                                         "user_agent"    => $locationStructure->userAgent,
                                         "transport"     => $locationStructure->transport
                                     );
            }
        }
    }

    function getVoicemailForwarding () {
        dprint("getVoicemailForwarding()");

        if (!$this->voicemail['Account']) {
            return;
        }

        if ($this->absolute_voicemail_uri) {
            $value=$this->voicemail['Account'];
        } else {
            $value="<voice-mailbox>";
        }

        return array("name"  => sprintf (_("Voice mailbox")),
                   "value" => $value,
                   "description"  => "Voicemail");
    }

    function showAboveTabs() {
        print "
        <tr>
        <td colspan=2>
        ";

        print "
        </td>
        </tr>
        ";

    }

    function showTabs() {
        print "
        <tr>
        <td colspan=2>
        ";

        print "
        <table class=border2 border=0 cellspacing=0 cellpadding=0 align=right>
        <tr>
        ";
    
        $items=0;
    
        while (list($k,$v)= each($this->tabs)) {
            if ($this->tab==$k) {
                $tabcolor="orange";
                $fontcolor="white";
            } else {
                $tabcolor="#4A69A5";
                $fontcolor="white";
            }
            print "<td class=border2 bgcolor=$tabcolor>&nbsp;";
            print "<a class=b href=$this->pageURL&tab=$k><font color=$fontcolor>$v</font>";
            print "&nbsp;</td>";
        }
        print "
        </tr>
        </table>
        ";
        print "
        </td>
        </tr>
        ";

    }

    function showUnderTabs() {
        print "
        <tr>
        <td colspan=2>
        ";
        print "
        </td>
        </tr>
        ";
    }

    function showSummary() {
        $this->getVoicemail();
        $this->getENUMmappings();
        $this->getAliases();

        $chapter=sprintf(_("SIP account"));
        $this->showChapter($chapter);

        print "
        <tr>
        <td class=border>";
        print _("Address");
        print "
        </td>
        <td class=border>sip:$this->account
        </td>
        </tr>
        ";
        print "
        <tr>
        <td class=border>";
        print _("Full name");
        print "
        </td>
        <td class=border>$this->fullName
        </td>
        </tr>
        <tr>
        <td class=border>";
        print _("Username");
        print "
        </td>
        <td class=border>$this->username
        </td>
        </tr>
        ";
        print "
        <tr>
        <td class=border>";
        print _("Domain");
        print "
        </td>
        <td class=border>$this->domain
        </td>
        </tr>
        ";

        print "
        <tr>
        <td class=border>";
        print _("Outbound proxy");
        print "
        </td>
        <td class=border>$this->sip_proxy
        </td>
        </tr>
        <tr>
          <td height=3 colspan=2></td>
        </tr>
        ";

        if ($this->presence_engine) {
            $chapter=sprintf(_("Presence settings"));
            $this->showChapter($chapter);

            print "
            <tr>
                <td class=border>Presence mode
                </td>
                <td class=border>Presence agent with XCAP policy
                </td>
            </tr>
            <tr>
                <td class=border>XCAP root URL
                </td>
                <td class=border>$this->xcap_root
            </td>
            </tr>
            ";

        }

        $chapter=sprintf(_("ENUM and aliases"));
        $this->showChapter($chapter);

        $t=0;
        foreach($this->enums as $e)  {
            $t++;
            print "
            <tr>
              <td class=border>ENUM $t</td>
              <td class=border>$e</td>
            </tr>
            ";
        }
        if (!$t) {
            print "
            <tr>
              <td class=border>ENUM</td>
              <td class=border>";
                print _("None");
                print "
              </td>
            </tr>
            ";
        }

        $t=0;

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        foreach($this->aliases as $a)  {
            $t++;

            print "
            <tr>
              <td class=border>";
                print _("Alias");
                print " $t
              </td>
              <td class=border> <input type=text size=35 name=aliases[] value=\"$a\"></td>
            </tr>
            ";
        }

        print "
        <tr>
          <td class=border>";
            print _("New alias");
            print "
          </td>
          <td class=border> <input type=text size=35 name=aliases[]></td>
        </tr>
        ";

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set aliases\">
        ";
        print "
        <input type=submit value=\"";
        print _("Save aliases");
        print "\"
               onClick=saveHandler(this)>
        ";
        print "
          </td>
          <td align=right>
          </td>
        </tr>
        ";

        print $this->hiddenElements;

        print "
        </form>
        ";

        $chapter=sprintf(_("Notifications address"));
        $this->showChapter($chapter);

        print "
        <tr>
          <td class=border>";
            print _("Email address");
            print "
          </td>
          <td class=border align=left>";

          if ($this->email) {
            print "$this->email";
          } else {
            print _("Unassigned");
          }
          print "
          </td>
        </tr>
        ";
        print "
        <tr>
          <td height=3 colspan=2></td>
        </tr>
        ";

        print "
        <form method=post>";

        print "
        <tr>
          <td align=left colspan=2>
        ";

        if ($this->email)  {
            printf (_("Email account information to %s"),$this->email);
            print "
            <input type=hidden name=action value=\"Send settings\">
            <input type=submit value=";
            print _("Send");
            print ">";
        }

        if ($this->sip_settings_page && $this->login_type != 'subscriber') {
            print "<p>";
            printf (_("Subscriber may login using the SIP credentials at:
            <a href=%s>%s</a>"),$this->sip_settings_page,$this->sip_settings_page);
        }

        print "
          </td>
          <td align=right>";
            print "
          </td>
        </tr>
        ";

        print $this->hiddenElements;

        print "
        </form>
        ";

    }

    function showFooter() {
        print "
        <tr>
          <td height=30 colspan=2 align=right valign=bottom>";

        if ($this->footerFile) {
            include ("$this->footerFile");
        } else {
            print "<a href=http://ag-projects.com target=agprojects><img src=images/PoweredbyAGProjects.gif border=0></a>";
        }

        print "</td>
        </tr>
        ";

    }

    function showSettings() {

        $this->getVoicemail();
        $this->getENUMmappings();

        $this->getDivertTargets();
        $this->getDiversions();

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        $chapter=sprintf(_("SIP account"));
        $this->showChapter($chapter);

        if ($this->login_type != "admin" && $this->login_type != "reseller") {
    
            print "
            <tr>
            <td class=border>";
            print _("Name");
            print "
            </td>
            <td class=borderns>";
    
            print "$this->firstName $this->lastName";
    
            print "
            </td>
            </tr>
            ";
    
        } else {
    
            print "
            <tr>
            <td class=border>";
            print _("First name");
            print "
            </td>
            <td class=borderns>";
    
            print "<input type=text size=15 name=first_name value=\"$this->firstName\">";
    
            print "
            </td>
            </tr>
            ";
    
            print "
            <tr>
            <td class=border>";
            print _("Last name");
            print "
            </td>
            <td class=borderns>";
    
            print "<input type=text size=15 name=last_name value=\"$this->lastName\">";
    
            print "
            </td>
            </tr>
            ";

        }

        print "
        <tr>
        <td class=border>";
        print _("Password");
        print "
        </td>
        <td class=borderns>";

        print "<input type=text size=15 name=sip_password>";
        printf ("\n\n<!-- \nSIP account password: %s\n -->\n\n",$this->password);

        print _("Language");
        print "
        <select name=language>
        ";
        $languages=array("en"=>"English",
                         "nl"=>"Nederlands",
                         "ro"=>"Romaneste",
                         "de"=>"Deutsch"
                         );

        $selected_lang[$this->Preferences['language']]="selected";

        while (list ($value, $name) = each($languages)) {
             print "<option value=$value $selected_lang[$value]>$name\n";
        }

        print "
        </select>
        ";

        print "
        </td>
        </tr>
        ";

        print "
        <tr>
        <td class=border>";
        print _("Timezone");
        print "
        </td>
        <td class=border>
        ";
        $this->showTimezones('timezone',$this->timezone);
        print " ";
        $timestamp=time();
        $LocalTime=getLocalTime($this->timezone,$timestamp);
        print _("Local time");
        print ": $LocalTime";
        //dprint_r($this->availableGroups);
        print "
        </td>
        </tr>
        ";

        if (count($this->emergencyRegions) > 0) {
            print "
            <tr>
            <td class=border>";
            print _("Location");
            print "
            </td>
            <td class=border>
            ";
            print "<select name=region>";
            $selected_region[$this->region]="selected";
            foreach (array_keys($this->emergencyRegions) as $_region) {
                printf ("<option value=\"%s\" %s>%s",$_region,$selected_region[$_region],$this->emergencyRegions[$_region]);
            }
            print "</select>";

            print "
            </td>
            </tr>
            ";
        }

        /*
        print "
        <tr>
        <td class=border>";
        print _("Language");
        print "</td>
        <td class=border>
        <select name=language>
        ";
        $languages=array("en"=>"English",
                         "nl"=>"Nederlands",
                         "ro"=>"Romaneste",
                         "de"=>"Deutsch"
                         );

        $selected_lang[$this->Preferences['language']]="selected";

        while (list ($value, $name) = each($languages)) {
             print "<option value=$value $selected_lang[$value]>$name\n";
        }                
        print "
        </select>
        </td>
        </tr>
        ";

        */

        if (in_array("free-pstn",$this->groups)) {
 
            if (in_array("quota",$this->groups)) {
                $td_class="orange";
            } else {
                $td_class="border";
            }

            if ($this->pstn_changes_allowed) {

                print "
                <tr>
                <td class=border>";
                print _("Quota");
                print "
                </td>
                <td class=$td_class>
                <table cellspacing=0 cellpadding=0 width=100%>
                <tr>
                <td>";
    
                printf ("<input type=text size=6 maxsize=6 name=quota value='%s'> %s",$this->quota,$this->currency);
     
                if ($this->quota || in_array("quota",$this->groups)) {
                    $this->getCallStatistics();
                    if ($this->thisMonth['price']) {
                        print "&nbsp;&nbsp;&nbsp;";
                        printf (_("This month usage: %.2f %s"),$this->thisMonth['price'], $this->currency);
                        printf (" / %d ",$this->thisMonth['calls']);
                        print _("Calls");
                    }
                }
     
                print "
                </td>
                <td align=right>
                ";

                if ($this->pstn_changes_allowed) {
                    print _("Reset");
                    print "
                    <input type=checkbox name=quota_reset value=1>
                    ";
                }
    
                print "</td>
                </tr>
                </table>
                </td>
                </tr>
                ";

            } else if ($this->quota) {
                print "
                <tr>
                <td class=border>";
                print _("Quota");
                print "
                </td>
                <td class=$td_class>
                <table cellspacing=0 cellpadding=0 width=100%>
                <tr>
                <td>";
                printf ("%s %s",$this->quota,$this->currency);
                print "</td>
                </tr>
                </table>
                </td>
                </tr>
                ";
            }
        }

        if ($this->pstn_access) {

            if ($this->prepaid) $checked_box_prepaid="checked";

            if (!$this->prepaid_changes_allowed) $disabled_box_prepaid   = "disabled=true";
    
            print "
            <tr $bgcolor>
            <td class=border valign=top>Prepaid</td>
            <td class=border>
            <input type=checkbox value=1 name=prepaid $checked_box_prepaid $disabled_box_prepaid>
            </td>
            </tr>
            ";
        }

        foreach (array_keys($this->availableGroups) as $key) {

            unset($disabled_box);

            if ($this->login_type == 'subscriber' && !$this->availableGroups[$key]['SubscriberMaySeeIt']) {
                continue;
            }

            if (in_array($key,$this->groups)) {
                $checked_box[$key]="checked";
            }

            $elementName    = $this->availableGroups[$key]["WEBName"];
            $elementComment = $this->availableGroups[$key]["WEBComment"];

            if ($this->login_type == 'subscriber') {
                if ($this->availableGroups[$key]['SubscriberMayEditIt']) {
                    $disabled_box = "";
                } else {
                    $disabled_box = "disabled=true";
                }
            }

            if ($key=="free-pstn" && !$this->pstn_changes_allowed) {
                $disabled_box   = "disabled=true";
            }

            if ($key=="blocked" && $checked_box[$key]) {
                $td_class="orange";
            } else {
                $td_class="border";
            }

            print "
            <tr $bgcolor>
            <td class=border valign=top>$elementName</td>
            <td class=$td_class>
            ";

            if ($key=="blocked") {
                if ($this->Preferences['blocked_by']) {
                    $selected_blocked_by[$this->Preferences['blocked_by']]='selected';
                }

                if ($this->login_type == 'admin' || $this->login_type == 'reseller') {

                    if ($this->customer != $this->reseller || $selected_blocked_by['customer']) {
                        printf ("
                        <select name=%s>
                        <option value=''>Active
                        <option value='customer' %s> Blocked by customer (%d)
                        <option value='reseller' %s> Blocked by reseller (%d)
                        </select>
                        ",
                        $key,
                        $selected_blocked_by['customer'],
                        $this->customer,
                        $selected_blocked_by['reseller'],
                        $this->reseller
                        );
                    } else if ($this->reseller) {
                        printf ("
                        <select name=%s>
                        <option value=''>Active
                        <option value='reseller' %s> Blocked by reseller (%d)
                        </select>
                        ",
                        $key,
                        $selected_blocked_by['reseller'],
                        $this->reseller
                        );
                    } else {
                        printf ("
                        <select name=%s>
                        <option value=''>Active
                        <option value='reseller' %s> Blocked
                        </select>
                        ",
                        $key,
                        $selected_blocked_by['reseller']
                        );
                    }

                } else if ($this->login_type == 'customer' ) {

                    if (in_array($key,$this->groups)) {
                       if ($this->Preferences['blocked_by'] != 'reseller') {
                           printf ("
                           <select name=%s>
                           <option value=''>Active
                           <option value='customer' %s> Blocked
                           </select>
                           ",
                           $key,
                           $selected_blocked_by['customer']
                           );
                       } else {
                           printf ("Blocked by reseller");
                       }
                   } else {
                       printf ("
                       <select name=%s>
                       <option value=''>Active
                       <option value='customer' %s> Blocked
                       </select>
                       ",
                       $key,
                       $selected_blocked_by['customer'],
                       $selected_blocked_by['reseller']
                       );
                   }
                } else {
                       if (in_array($key,$this->groups)) {
                           printf ("Blocked");
                    } else {
                           printf ("Active");
                    }
                }

            } else if ($key=="free-pstn") {
                print "
                <input type=checkbox value=1 name=$key $checked_box[$key] $disabled_box>
                ";

                if ($this->pstn_changes_allowed) {
                    print "$elementComment: <input type=text size=15 maxsize=15 name=rpid value=\"$this->rpid\">";
                } else {
                    if ($this->rpid) {
                        print "$elementComment: $this->rpid";
                    } else {
                        print "$elementComment";
                    }
                }

            } else {
                print "
                <input type=checkbox value=1 name=$key $checked_box[$key] $disabled_box> $elementComment
                ";
            }

            print "
            </td>
            </tr>
            ";
        }

        $this->showExtraGroups();

        $this->showOwner();

        if ($this->Preferences['advanced']) {
            $this->showQuickDial();
            $this->showMobileNumber();
            $this->showVoicemail();
        }

        $this->showBillingProfiles();

        if (!$this->Preferences['advanced']) {
            print "
            <tr>
            <td class=border>";
            print _("Advanced options");
            print "
            </td>
            <td class=ag1>";
            print "<input type=checkbox name=advanced value=1> ";
            print _("Check this box and click save to enable advanced options");
            print "
            </td>
            </tr>
            ";
        }

        if ($this->Preferences['advanced']) {
            $chapter=sprintf(_("Call diversion"));
            $this->showChapter($chapter);

            $this->showDiversions();
        }

        $chapter=sprintf(_("Notifications address"));
        $this->showChapter($chapter);

        print "
        <tr>
          <td class=border>";
            print _("Email address");
            print "
          </td>
          <td class=border align=left>
            <input type=text size=40 maxsize=255 name=mailto value=\"$this->email\">
          </td>
        </tr>
        ";
        print "
        <tr>
          <td height=3 colspan=2></td>
        </tr>
        ";

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"Save settings\">
        ";

        print "
        <input type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";
        print "
          </td>
          <td align=right>
          </td>
        </tr>
        ";

        print $this->hiddenElements;

        print "
        </form>
        ";

    }

    function showVoicemail() {

        if ($this->voicemail['Account']) {
            $checked_voicemail="checked";
        }

        $chapter=sprintf(_("Voicemail"));
        $this->showChapter($chapter);

        print "
        <tr $bgcolor>
        <td class=border>";
        print _("Enable");
        print "</td>
        <td class=border>
        <input type=checkbox value=1 name=voicemail $checked_voicemail $disabled_box>
        ";

        if ($this->voicemail['Account'] &&
            ($this->login_type == 'admin' || $this->login_type == 'reseller')) {
            printf (" (Mailbox %s) ",$this->voicemail['Account']);
        }

        print "
        </td>
        </tr>
        ";

        if ($this->voicemail['Account']) {

            print "
            <tr $bgcolor>
            <td class=border>";

            print _("Delivery");
            print "</td>
            <td class=border>
            ";

            if ($this->voicemail['Options']->delete=="True") {
                $checked_delete_voicemail="checked";
                $selected_store_voicemail['email']  ="selected";
                $selected_store_voicemail['server'] ="";
            } else {
                $selected_store_voicemail['email']  ="";
                $selected_store_voicemail['server'] ="selected";
            }

            if (!$this->voicemail['DisableOptions']) {
                print "<select name=delete_voicemail>";
                $_text=sprintf("Send messages by e-mail to %s",$this->email);
                printf ("<option value=1 %s>%s",$selected_store_voicemail['email'],$_text);
                printf ("<option value=0 %s>%s",$selected_store_voicemail['server'],_("Send messages by e-mail and store messages on the server"));
                print "</select>";
                print "<br>";
            } else {
                printf (_("Voice messages are sent by email to %s"),$this->email);
            }

            print "
            </td>
            </tr>
            ";

            if (!$this->voicemail['DisableOptions']) {

                print "
                <tr $bgcolor>
                <td class=border>";
    
                print _("Password");
                print "</td>
                <td class=border>
                ";
    
                printf ("<input type=text size=15 name=voicemail_password value=\"%s\">",$this->voicemail['Password']);
    
                print "
                </td>
                </tr>
                ";
    
                if ($this->voicemail_access_number) {
                   print "
                   <tr $bgcolor>
                   <td colspan=2 class=border>";
                   printf(_("Dial %s to listen to your messages or change preferences. "),$this->voicemail_access_number);
                   print "</td>
                   </tr>
                   ";
                }
            }
        }
    }

    function showOwner() {

        if ($this->pstn_changes_allowed) {
            print "
            <tr $bgcolor>
            <td class=border>";
            print _("Owner");
            print "</td>
            <td class=border>
            <input type=text name=owner size=7 value=\"$this->owner\">
            ";
            print "
            </td>
            </tr>
            ";
        } else {
            print "
            <tr $bgcolor>
            <td class=border>";
            print _("Owner");
            print "</td>
            <td class=border>
            $this->owner
            ";
            print "
            </td>
            </tr>
            ";
        }
    }

    function showDeviceLocations() {
        $this->getDeviceLocations();

        if (count($this->locations)) {
            print "
            <tr>
              <td height=3 colspan=2></td>
            </tr>
            <tr>
              <td class=border colspan=2 bgcolor=lightgrey>";
                print "
                <table border=0 cellpadding=0 cellspacing=0 width=100%>
                <tr>
                <td align=left>
                ";
                print "<b>";
                print _("Devices");
                print "</b>
                </td>
                <td align=right><b>";
                print _("Expires in");
                print "</b>
                </td>
                </tr>
                </table>
                ";
                print "
              </td>
            </tr>
            ";

            $j=0;

            foreach (array_keys($this->locations) as $location) {
                $j++;
                $contact       = $this->locations[$location]['contact'];
                $publicContact = $this->locations[$location]['publicContact'];
                $expires       = normalizeTime($this->locations[$location]['expires']);
                $user_agent    = $this->locations[$location]['user_agent'];
                $transport     = $this->locations[$location]['transport'];
                $UAImage       = $this->getImageForUserAgent($user_agent);

                print "<tr>";
                print "<td class=border align=center>";
                if (preg_match("/(unidata|snom|eyebeam|csco)/i",$user_agent,$m)) {
                    $_ua=strtolower($m[1]);
                    print "<a href=$this->pageURL&tab=phonebook&export=1&userAgent=$_ua target=export>";
                    printf("<img src='%s/30/%s' border=0>",$this->SipUAImagesPath,$UAImage);
                    print "</a>";
                } else {
                    printf ("<img src='%s/30/%s' border=0>",$this->SipUAImagesPath,$UAImage);
                }
                print "</td>";
                print "<td class=border align=left>";
                    print "<table border=0 width=100%>";
                    print "<tr>";
                        print "<td align=left><i>$user_agent</i></td>";
                        print "<td align=right>";
                        if ($transport == 'tls') print "<img src=images/lock15.gif border=0><br>";
                        print "</td>";
                    print "</tr>";
                    print "<tr>";
                        print "<td align=left>";
                        print _("Location");
                        print ": ";
                        if (strlen($transport)) print "$transport:";
                        print "$contact ";
                        if ($publicContact != $contact) {
                            print " ($publicContact)";
                        }
                        print "</td>";
                        print "<td align=right>$expires</td>";
                    print "</tr>";
                    print "</table>";
                print "</td>";
                print "</tr>";
            }
        }
    }

    function getBarringPrefixes() {
        dprint("getBarringPrefixes()");

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getBarringPrefixes($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        $this->barringPrefixes=$result;
        return true;
    }

    function setBarringPrefixes() {
        dprint("setBarringPrefixes");
        $prefixes=array();
        $barringPrefixes=$_REQUEST['barringPrefixes'];

        foreach ($barringPrefixes as $_prefix) {
            if (preg_match("/^\+[1-9][0-9]*$/",$_prefix)) {
                $prefixes[]=$_prefix;
            }
        }

        dprint("setBarringPrefixes");

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->setBarringPrefixes($this->sipId,$prefixes);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }
    }

    function showBarringPrefixes() {
        $chapter=sprintf(_("Blocked destinations"));
        $this->showChapter($chapter);

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        print "
        <tr>
        <td class=border colspan=2 align=left>";
        print _("You can use this feature to deny calls to expensive or unwanted destinations on the classic telephone network. ");
        print "<p>";
        print "
        </td>
        </tr>
        ";

        print "
        <tr>
        <td class=border align=left>";
        print _("Destination prefix");
        print "</td>";
        print "<td class=border align=left>
        <input type=text name=barringPrefixes[]>
        ";
        print _("Example: +31900");
        print "
        </td>
        </tr>
        ";

        if ($this->getBarringPrefixes()) {
            foreach ($this->barringPrefixes as $_prefix) {
                print "<tr>";
                print "<td class=border align=left>";
                print _("Destination prefix");
                print "</td>";
                print "<td class=border align=left>
                <input type=text name=barringPrefixes[] value=\"$_prefix\">
                </td>";
                print "<tr>";
            }
        }

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set barring prefixes\">
        ";

        print "
        <input type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";

        print "
          </td>
          <td align=right>
          </td>
        </tr>
        ";

        print $this->hiddenElements;

        print "
        </form>
        ";
    }

    function update() {
        dprint("update()");

        $this->getVoicemail();
        $this->getENUMmappings();

        $this->getDivertTargets();
        $this->getDiversions();

        foreach ($this->WEBdictionary as $el) {
            ${$el} = $_REQUEST[$el];
        }

        $newACLarray = array();

        $result      = $this->result;

        if (!is_array($result->properties))   $result->properties=array();
        if (!is_array($result->groups))       $result->groups=array();

        if ($mailto && $this->email != $mailto) {
            $result->email=$mailto;
            $this->email=$mailto;
            $this->somethingChanged=1;
            $this->voicemailOptionsHaveChanged=1;
        }

        if ($this->login_type == "admin" || $this->login_type == "reseller") {
            if ($first_name && $this->firstName != $first_name) {
                $result->firstName = $first_name;
                $this->firstName  = $first_name;
                $this->somethingChanged=1;
                $this->voicemailOptionsHaveChanged=1;
            }
    
            if ($last_name && $this->lastName != $last_name) {
                $result->lastName = $last_name;
                $this->lastName  = $last_name;
                $this->somethingChanged=1;
            }
        }

        $this->properties=$result->properties;

        if ($advanced) {
            if (!$this->Preferences['advanced']) {
                $this->setPreference("advanced","1");
                $this->somethingChanged=1;
            }

            $voicemail=1;
            $this->availableGroups['voicemail']=array("Group"=>"voicemail",
                                        "WEBName" =>sprintf (_("Voice mailbox")),
                                        "SubscriberMayEditIt"=>"1",
                                        "SubscriberMaySeeIt"=>0
                                        );
        }

        if (!$this->voicemail['Account'] && $voicemail) {
            if ($this->addVoicemail()) {
                $this->setVoicemailDiversions();
                $this->createdVoicemailnow=1;
            }
        } else if ($this->voicemail['Account'] && !$voicemail) {
            if ($this->deleteVoicemail()) {
                $this->voicemail['Account']="";
            }
        }

        if ($this->pstn_changes_allowed) {
            if (strcmp($quota,$this->quota) != 0) {
                if (!$quota) $quota=0;
                $result->quota=intval($quota);
                dprint ("change the quota");
                $this->somethingChanged=1;
            }

            if ($quota_reset) {
                $result->groups = array_unique(array_diff($this->groups,array('quota')));
                $this->somethingChanged=1;

                $this->SipPort->addHeader($this->SoapAuth);
                $this->SipPort->removeFromGroup(array("username" => $this->username,"domain"=> $this->domain), "quota");
            }

            if (strcmp($rpid,$this->rpid) != 0) {
                dprint ("change the rpid");
                $result->rpid=$rpid;
                $this->somethingChanged=1;
            }

            $owner=intval($owner);
            if ($owner != $this->owner) {
                dprint ("change the owner");
                $result->owner=$owner;
                $this->somethingChanged=1;
            }
        }

        if ($this->prepaid_changes_allowed) {
            if(!$result->prepaid && $_REQUEST['prepaid']){
                if ($result->quota) {
                    $this->somethingChanged=1;
                }
                $this->somethingChanged=1;
            } else if ($result->prepaid && !$_REQUEST['prepaid']) {
                $this->somethingChanged=1;
            }

            $result->prepaid=intval($_REQUEST['prepaid']);
        }

        reset($this->availableGroups);

        foreach (array_keys($this->availableGroups) as $key) {
            // $val is set to 1 if web checkbox is ticked
            $val = $_REQUEST[$key];

            if ($this->login_type != 'subscriber' || $this->availableGroups[$key]['SubscriberMayEditIt']) {

                if ($key == 'free-pstn') {
                	if (in_array($key,$this->groups) && !$val) {
                    	if ($this->quota) {
                            // we save quota for later use when pstn access is re-granted
                            $this->somethingChanged=1;
                            $this->setPreference('last_sip_quota',"$this->quota");
                        }
                    }

                	if (!in_array($key,$this->groups) && $val) {
                    	$this->setPreference('last_sip_quota',strval($this->quota));
                    
                        $last_sip_quota=$this->Preferences['last_sip_quota'];
                        if ($last_sip_quota) {
                            $result->quota=intval($last_sip_quota);
                            $this->somethingChanged=1;
                        }
                    }

                    if ($this->pstn_changes_allowed) {
                        if ($val) $newACLarray[]=trim($key);
                    } else {
                        if (in_array($key,$this->groups)) {
                            $newACLarray[]=trim($key);
                        }
                    }

                } else if ($key == 'blocked') {
                    if ($this->login_type == 'admin' || $this->login_type == 'reseller') {
    
                        if ($val && $val != $this->Preferences['blocked_by']) {
                            $this->setPreference('blocked_by',$val);
                            $this->somethingChanged=1;
    
                        } else if (!$val && in_array($key,$this->groups)) {
                            $this->somethingChanged=1;
                            $this->setPreference('blocked_by','');
                        }
    
                        if ($val) $newACLarray[]=trim($key);

                    } else if ($this->login_type == 'customer' ) {
                        if ($this->Preferences['blocked_by'] != 'reseller') {
                            if ($val && ($val != $this->Preferences['blocked_by'] || !in_array($key,$this->groups) )) {
                                $this->setPreference('blocked_by',$val);
                                $this->somethingChanged=1;
                                $newACLarray[]=trim($key);
                            } else if (!$val && in_array($key,$this->groups)) {
                                $this->somethingChanged=1;
                                $this->setPreference('blocked_by','');
                            }
       
                            if ($val) $newACLarray[]=trim($key);
                        } else {
                            // copy old setting if exists
                            if (in_array($key,$this->groups)) {
                                $newACLarray[]=trim($key);
                            }
                        }
                     }
                } else {
                    if ($val) $newACLarray[]=trim($key);
                }

            } else {
                // copy old setting if exists
                if (in_array($key,$this->groups)) {
                    $newACLarray[]=trim($key);
                }
            }
        }

		$foundGroupInAvailableGroups=array();

        $extra_groups=explode(' ',$_REQUEST['extra_groups']);

        foreach ($extra_groups as $_grp) {
            if (!in_array($_grp,array_keys($this->availableGroups))) {
            	$newACLarray[]=$_grp;
            }
        }

        $grantACLarray  = array_unique(array_diff($newACLarray,$this->groups));
        $revokeACLarray = array_unique(array_diff($this->groups,$newACLarray));

        /*
        dprint_r($this->groups);
        dprint_r($newACLarray);
        dprint_r($grantACLarray);
        dprint_r($revokeACLarray);
        */

        if (count($revokeACLarray) || count($grantACLarray)) {
            $result->groups=$newACLarray;
            $this->somethingChanged=1;
        }

        if ($language && $language != $this->Preferences['language'] ) {
            if ($this->login_type == 'subscriber') {;
                dprint("Set lang $language");
                changeLanguage($language);
            }

            $this->setPreference("language",$language);
            $this->somethingChanged=1;
        }

        if ($sip_password) {
        	if ($this->store_clear_text_passwords) {
            	$result->password=$sip_password;
            } else {
                $md1=strtolower($this->username).':'.strtolower($this->domain).':'.$sip_password;
                $md2=strtolower($this->username).'@'.strtolower($this->domain).':'.strtolower($this->domain).':'.$sip_password;
                $result->password=md5($md1).':'.md5($md2);
            }

            $this->somethingChanged=1;
        }

        if (!$result->password) unset($result->password);

        if ($timezone && $timezone != $this->timezone) {
            $result->timezone=$timezone;
            $this->somethingChanged=1;
        }

        if ($region != $this->region) {
            $result->region=$region;
            $this->somethingChanged=1;
        }

        if ($this->Preferences['advanced']) {
    
            if (strcmp($quickdial,$this->quickdial) != 0) {
                if ($this->SOAPversion > 1) {
                    $result->quickdialPrefix=$quickdial;
                } else {
                    $result->quickdial=$quickdial;
                }

                $this->somethingChanged=1;
            }

            $mobile_number  = preg_replace("/[^\+0-9]/","",$mobile_number);
            if ($this->Preferences['mobile_number'] != $mobile_number) {
                $this->setPreference('mobile_number',$mobile_number);
                $this->somethingChanged=1;
            }

            if (!$this->createdVoicemailnow) {
                $this->setDiversions();
            }

            if ($this->timeoutWasNotSet || $timeout != $this->timeout) {
                $this->somethingChanged=1;
                if ($this->SOAPversion > 1) {
                    $result->timeout=intval($timeout);
                } else {
                    $result->answerTimeout=intval($timeout);
                }
            }
        }

        if ($this->SOAPversion > 1) {
            $result->timeout=intval($result->timeout);
        } else {
            $result->answerTimeout=intval($result->answerTimeout);
        }

        if ($this->somethingChanged) {

        	$result->properties=$this->properties;

        	if (!$result->quota) $result->quota=0;

             //dprint_r($result);

            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->updateAccount($result);

            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            } else {
                dprint("Call updateAccount");
            }

        }

        if ($this->voicemail['Account'] && !$this->createdVoicemailnow) {
            $delete_voicemail = $_REQUEST['delete_voicemail'];

            //$log=sprintf("delete_voicemail_orig=%s",$this->voicemail['Options']->delete);
            //dprint($log);

            if (($delete_voicemail && !$this->voicemail['Options']->delete) ||
                (!$delete_voicemail && $this->voicemail['Options']->delete)) {
                $this->voicemail['Options']=array("delete"=>intval($delete_voicemail));
                $this->voicemailOptionsHaveChanged=1;
            }

            $voicemail_password=preg_replace("/[^0-9]/","",$voicemail_password);

            if ($this->voicemail['Password'] != $voicemail_password) {
                $this->voicemailOptionsHaveChanged=1;
                $this->voicemail['Password']=$voicemail_password;
            }

            if ($this->voicemailOptionsHaveChanged) {
                $this->updateVoicemail();
            }
        }

        $this->updateBillingProfiles();

    }

    function setDiversions() {
        dprint ("setDiversions()");

        if (!$this->Preferences['advanced']) {
            return true;
        }

        foreach (array_keys($this->CallPrefDictionary) as $condition) {
            $select_name = $condition."_select";
            $selectedIdx = $_REQUEST[$select_name];
            $textboxURI  = $_REQUEST[$condition];

            if ($textboxURI && $textboxURI != "<voice-mailbox>" && !preg_match("/@/",$textboxURI)) {
                $textboxURI=$textboxURI."@".$this->domain;
            }

            if (preg_match("/^([\+|0].*)@/",$textboxURI,$m))  {
                $textboxURI=$m[1]."@".$this->domain;
            }

            $uri_description = $this->divertTargets[$selectedIdx]['description'];

            if ($uri_description == 'Other' || $textboxURI == "<voice-mailbox>") {
                $selectedURI = $textboxURI;
            } else {
                $selectedURI = $this->divertTargets[$selectedIdx]['value'];
            }

            $uri = $selectedURI;

            if (!$this->voicemail['Account'] && $uri_description == 'Voicemail') {
                dprint("No voicemail account found");
                $uri_description='Disabled';
            }

            if ($this->CallPrefDbURI[$condition]) {

                if ($uri_description=='Disabled' && $this->CallPrefUriType[$condition]!='Disabled') {
                    $diversions[$condition]="";
                } else if ($uri_description != 'Disabled' && $selectedURI) {
                    if (checkURI($selectedURI)) {
                        if ($this->CallPrefUriType[$condition]=='Disabled') {
                            $diversions[$condition]="";
                        } else {
                            if ($this->CallPrefDbURI[$condition] != $uri) {
                                $diversions[$condition]=$uri;
                            } else {
                                $diversions[$condition]=$this->CallPrefDbURI[$condition];
                            }
                        }

                    } else {
                           $diversions[$condition]=$this->CallPrefDbURI[$condition];
                        dprint("Failed to check address $selectedURI");
                    }
                }
            } else if ($uri_description!='Disabled' && $selectedURI) {
                if (checkURI($selectedURI)) {
                    $diversions[$condition]=$uri;
                   } else {
                       dprint("Failed to check address $condition=\"$selectedURI\"");
                       $diversions[$condition]=$this->CallPrefDbURI[$condition];
                   }
            }

            if (!$uri_description) $uri_description="Other";

            if ($uri_description == 'Other') {
                $last_other=$uri;
            } else {
                $last_other = $this->CallPrefLastOther[$condition];
            }

            $_prefLast   = $condition."_lastOther";

            if ($uri_description=='Other' && $this->Preferences[$_prefLast] != $last_other ) {
                $this->setPreference($_prefLast,$last_other);
            }
        }

        foreach(array_keys($this->CallPrefDbURI) as $key) {
            if ($this->CallPrefDbURI[$key] != $diversions[$key]) {
                //$log=sprintf("Diversion %s changed from %s to %s",$key,htmlentities($this->CallPrefDbURI[$key]),htmlentities($diversions[$key]));
                dprint($log);
                $divert_changed=1;
            }

            if ($diversions[$key]) {
                if ($diversions[$key] == "<voice-mailbox>") {
                    if ($this->absolute_voicemail_uri) {
                        $diversionsSOAP[$key] = 'sip:'.$this->voicemail['Account'];
                    } else {
                        $diversionsSOAP[$key] = $diversions[$key];
                    }
                } else {
                    $diversionsSOAP[$key]='sip:'.$diversions[$key];
                }

             } else {
                if ($diversions[$key]) $diversionsSOAP[$key]=$diversions[$key];
            }
        }

        if (!is_array($diversionsSOAP) || count($diversionsSOAP) == 0) {
            $diversionsSOAP=array("nocondition"=>"empty");
        }

        if ($divert_changed) {
            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->setCallDiversions($this->sipId,$diversionsSOAP);
    
            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error2 (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        }
    }

    function setDiversion($condition,$uri) {
        dprint ("setDiversion($condition,$uri)");
        $condition=trim($condition);
        $uri=trim($uri);
        $this->getVoicemail();
        $this->getDivertTargets();

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getCallDiversions($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        $_uri_saved=$uri;

        if ($_uri_saved == "voicemail") {
            $uri="";
            foreach ($this->divertTargets as $target) {
                if ($target['description'] == 'Voicemail') {
                    $uri=$target['value'];
                    break;
                }
            }
        }

        if ($_uri_saved == "mobile") {
            $uri="";
            foreach ($this->divertTargets as $target) {
                dprint_r($target);
                if ($target['description'] == 'Mobile') {
                    $uri=$target['value'];
                    break;
                }
            }
        }

        if ($_uri_saved == "other" && $this->CallPrefLastOther[$condition]) {
            $uri=$this->CallPrefLastOther[$condition];
        }


        if (strlen($uri)) {
             if ($uri != "<voice-mailbox>") {
                if (!preg_match("/^(sip:|sips:)/",$uri)) $uri="sip:".$uri;
             }
        } else {
            $uri=NULL;
        }

        reset($this->CallPrefDictionary);
        foreach(array_keys($this->CallPrefDictionary) as $_condition) {
            $uri=$result->$_condition;

            if ($this->absolute_voicemail_uri && $uri == "<voice-mailbox>") {
                $uri = $this->voicemail['Account'];
            }

            if (preg_match("/^(sip:|sips:)(.*)$/i",$uri,$m)) {
                $uri=$m[2];
            }

            //if (!$uri) $uri=NULL;
            $this->CallPrefDbURI[$condition]=$uri;
        }

        dprint_r($this->CallPrefDbURI);

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->setCallDiversions($this->sipId,$result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

    }

    function setVoicemailDiversions() {
        dprint ("setVoicemailDiversions()");

        if ($this->getVoicemail()) {

            if (!$this->absolute_voicemail_uri) {
                $diversions['FBUS']="<voice-mailbox>";
                $diversions['FNOA']="<voice-mailbox>";
                $diversions['FNOL']="<voice-mailbox>";
            } else {
                $diversions['FBUS']="sip:".$this->voicemail['Account'];
                $diversions['FNOA']="sip:".$this->voicemail['Account'];
                $diversions['FNOL']="sip:".$this->voicemail['Account'];
            }

            dprint("setDiversions");

            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->setCallDiversions($this->sipId,$diversions);
    
            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        }
    }

    function updateVoicemail() {
        dprint("updateVoicemail()");

        $account=array("sipId"    => $this->sipId,
                       "email"    => $this->email,
                       "name"     => $this->firstName.' '.$this->lastName,
                       "password" => $this->voicemail['Password'],
                       "options"  => $this->voicemail['Options']
                       );
 
        dprint_r($account);
 
        $this->VoicemailPort->addHeader($this->SoapAuthVoicemail);
        $result     = $this->VoicemailPort->updateAccount($account);
 
        if (PEAR::isError($result)) {
            $error_msg=$result->getMessage();
            $error_fault=$result->getFault();
            $error_code=$result->getCode();
            print "$error_msg\n";
            printf ("<p><font color=red>Error (VoicemailPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }
 
        return true;
    }

    function addVoicemail() {
        dprint("addVoicemail()");

        $password=$this->RandomPassword();
        
        $_account      = array("sipId"    => $this->sipId,
                               "name"     => $this->fullName,
                               "password" => $password,
                               "email"    => $this->email,
                               "options"  => array("delete"=>1)

        );

        $this->VoicemailPort->addHeader($this->SoapAuthVoicemail);
        $result     = $this->VoicemailPort->addAccount($_account);

        if (PEAR::isError($result)) {
            $error_msg=$result->getMessage();
            $error_fault=$result->getFault();
            $error_code=$result->getCode();
            print "$error_msg\n";
            printf ("<p><font color=red>Error (VoicemailPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;
    }


    function deleteVoicemail() {
        dprint("deleteVoicemail()");
        
        $this->VoicemailPort->addHeader($this->SoapAuthVoicemail);
        $result     = $this->VoicemailPort->deleteAccount($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg=$result->getMessage();
            $error_fault=$result->getFault();
            $error_code=$result->getCode();
            print "$error_msg\n";
            printf ("<p><font color=red>Error (VoicemailPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;
    }

    function setPreference($name,$value) {
        dprint("setPreference($name,$value)");

        if (!$name) return;

        if (!is_array($this->properties)) {
            $this->properties=array();
        }

        foreach (array_keys($this->properties) as $_key) {

           $_prop=$this->properties[$_key];
            if ($_prop->name == $name) {
                if (strlen($value)) {
                    $newProperties[]=array('name'=> $name,
                                           'value' => $value);
                }
                $found=1;
            } else {
                $newProperties[]=$_prop;
            }

        }

        if (!$found) {
            $newProperties[]=array('name'  => $name,
                                   'value' => $value);

        }

        if ($this->properties!=$newProperties) $this->somethingChanged=1;
        if (!$newProperties) $newProperties = array();
        $this->properties=$newProperties;
        //dprint_r($this->properties);
    }

    function showPrepaidForm() {
        $task        = $_REQUEST['task'];
        $issuer      = $_REQUEST['issuer'];
        $_done       = false;

        print "
        <tr>
        <form action=$this->pageURL method=post>
        <input type=hidden name=tab value=prepaid>
        <input type=hidden name=task value=Add>
        <td class=h colspan=2 align=left> ";

        if ($issuer=='subscriber'){
            $prepaidCard = $_REQUEST['prepaidCard'];
            $prepaidId   = $_REQUEST['prepaidId'];

            if ($prepaidCard && $prepaidId) {
            	if ($result = $this->addBalanceSubscriber($prepaidCard,$prepaidId)) {
                    print "<p><font color=green>";
                	printf (_("Old balance was %s, new balance is %s. "),$result->old_balance, $result->new_balance);
                    print "</font>";
                    $_done=true;
                }
            }
        }

        if ($issuer=='reseller' || $issuer=='admin') {
            $description = $_REQUEST['description'];
            $value       = $_REQUEST['value'];

            if (strlen($value) && $result = $this->addBalanceReseller($value,$description)) {

                print "<p><font color=green>";
                printf (_("Old balance was %s, new balance is %s. "),$result->old_balance, $result->new_balance);
                print "</font>";
                $_done=true;
            }
        }

        if ($_done && $_REQUEST['notify']) {
            $subject=sprintf ("SIP account %s balance update",$this->account);
    
            $body="Your SIP account balance has been updated. ".
            "For more details go to $this->sip_settings_page?tab=prepaid";
    
            if (mail($this->email, $subject, $body, "From: $this->support_email")) {
                print "<p><font color=orange>";
                printf (_("Subscriber has been notified to %s."), $this->email);
                print "</font>";
            }
        }

        print "
        </td>
        </tr>
        ";

        $prepaidAccount=$this->getPrepaidStatus();

        if ($prepaidAccount) {
            $chapter=sprintf(_("Current balance"));
            $this->showChapter($chapter);
    
            print "
            <tr>
            <td class=h align=left>";
            dprint_r($prepaidAccount);
            print _("Balance");
            print ": $prepaidAccount->balance $this->currency";
            print "</td><td align=right>
            </td>
            </form>
            </tr>
            ";
    
            $this->showIncreaseBalanceReseller();
            $this->showIncreaseBalanceSubscriber();
            $this->showBalanceHistory();
        }
    }

    function showIncreaseBalanceReseller () {
    	if ($this->login_type != 'reseller' && $this->login_type != 'admin') return true;

        $chapter=sprintf(_("Add balance (admin)"));
        $this->showChapter($chapter);

        print "
        <tr>
        <form action=$this->pageURL method=post>
        <input type=hidden name=tab value=prepaid>
        <input type=hidden name=issuer value=reseller>
        <input type=hidden name=task value=Add>
        <td class=h align=left><nobr>
        ";

        print _("Value");
        print "
        <input type=text size=10 name=value>
        ";
        print _("Description");

        print "
        <input type=text size=30 name=description>
        Notify
        <input type=checkbox name=notify value=1>

        <input type=submit value=";
        print _("Add");
        print ">
        </td>
        </form>
        </tr>
        ";

    }

    function showIncreaseBalanceSubscriber () {

        $chapter=sprintf(_("Add balance"));
        $this->showChapter($chapter);

        print "
        <tr>
        <form action=$this->pageURL method=post>
        <input type=hidden name=tab value=prepaid>
        <input type=hidden name=issuer value=subscriber>
        <input type=hidden name=task value=Add>
        <td class=h align=left><nobr>
        ";

        print _("Card id");
        print "
        <input type=text size=10 name=prepaidId>
        ";
        print "Number
        <input type=text size=20 name=prepaidCard>
        ";

        if ($this->login_type != 'subscriber') {
            print "
            Notify
            <input type=checkbox name=notify value=1>
            ";
        }

        print "
        <input type=submit value=";
        print _("Add");
        print "></nobr>
        </td>
        </form>
        </tr>
        ";
    }

    function getPrepaidStatus() {
        dprint("getPrepaidStatus()");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getPrepaidStatus(array($this->sipId));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }  else {
        	return $result[0];
        }
    }

    function addBalanceSubscriber($prepaidCard,$prepaidId) {
        dprint("addBalanceSubscriberLocal($prepaidCard,$prepaidId)");

        $card      = array('id'     => intval($prepaidId),
                           'number' => $prepaidCard
                           );

        $this->SipPort->addHeader($this->SoapAuth);
        $result = $this->SipPort->addBalanceFromVoucher($this->sipId,$card);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }  else {
            return $result;
        }
    }

    function addBalanceReseller($value=0,$description='') {
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->addBalance($this->sipId,floatval($value),$description);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
        	return $result;
        }
    }

    function getBalanceHistory() {
        $this->SipPort->addHeader($this->SoapAuth);

        $result     = $this->SipPort->getCreditHistory($this->sipId,20);
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "2000") {
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            }
        }

        $this->balance_history=$result->entries;
    }

    function showBalanceHistory() {
    	$this->getBalanceHistory();
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "2000") {
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            }
        }

        if (!count($this->balance_history)) {
            return;
        }

        $chapter=sprintf(_("Balance history"));
        $this->showChapter($chapter);

        print "
        <tr>
        <td colspan=2>
        ";

        print "
        <p>
        <table width=100% cellpadding=1 cellspacing=1 border=0 bgcolor=lightgrey>";
        print "<tr bgcolor=#CCCCCC>";
        print "<td class=h>";
        print _("Id");
        print "</td>";
        print "<td class=h>";
        print _("Date");
        print "</td>";
        print "<td class=h>";
        print _("Action");
        print "</td>";
        print "<td class=h>";
        print _("Description");
        print "</td>";
        print "<td class=h align=right>";
        print _("Value");
        print "</td>";
        print "<td class=h align=right>";
        print _("Balance");
        print "</td>";
        print "</tr>";

        foreach ($this->balance_history as $_line) {

			if (strstr($_line->description,'Session')) {
            	if (!$_line->value) continue;
                $value=$_line->value;

                if ($this->cdrtool_address) {
                    $description=sprintf("<a href=%s/callsearch.phtml?action=search&call_id=%s target=cdrtool>$_line->description</a>",$this->cdrtool_address,urlencode($_line->session));
                } else {
                    $description=$_line->description;
                }
            } else {
                $description=$_line->description;
                $value=$_line->value;
            }

            if ($value <0) {
                $total_debit+=$value;
            }
            if ($value >0) {
                $total_credit+=$value;
            }

            $found++;

            printf ("
            <tr bgcolor=white>
            <td>%d</td>
            <td>%s</td>
            <td>%s</td>
            <td>%s</td>
            <td align=right>%s</td>
            <td align=right>%s</td>
            </tr>
            ",$found,
            $_line->date,
            $_line->action,
            $description,
            number_format($value,4),
            number_format($_line->balance,4)
            );
        }

        print "
        </td>
        </tr>
        ";

        if (strlen($total_credit)) {

        printf ("
            <tr bgcolor=white>
            <td></td>
            <td></td>
            <td></td>
            <td>Total credit</td>
            <td align=right>%s</td>
            <td align=right></td>
            </tr>
            ",number_format($total_credit,4));
        }

        if (strlen($total_debit)) {
        printf ("
            <tr bgcolor=white>
            <td></td>
            <td></td>
            <td></td>
            <td>Total debit</td>
            <td align=right>%s</td>
            <td align=right></td>
            </tr>
            ",number_format($total_debit,4));
        }

        print "
        </table>
        ";

		if ($found) {
        	print "<p><a href=$this->pageURL&tab=prepaid&export=1 target=_new><font color=$fontcolor>Export history to CSV file</font>";
        }
        print "</td></tr>";
    }

    function exportBalanceHistory() {
        Header("Content-type: text/csv");
    	$h=sprintf("Content-Disposition: inline; filename=%s-prepaid-history.csv",$this->account);
    	Header($h);

    	$this->getBalanceHistory();

        $this->SipPort->addHeader($this->SoapAuth);

        $result     = $this->SipPort->getCreditHistory($this->sipId,200);
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "2000") {
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            }
        }

        printf ("Id,Account,Date,Action,Description,Transaction value,Final balance\n");
        foreach ($this->balance_history as $_line) {
			if (strstr($_line->description,'Session') && !$_line->value) continue;
            $found++;
            printf ("%s,%s,%s,%s,%s,%s,%s\n",
            $found,
            $this->account,
            $_line->date,
            $_line->action,
            $_line->description,
            $_line->value,
            $_line->balance);
        }
    }

    function showDiversions($conditions=array()) {
        // for busy not online or unconditional
        foreach (array_keys($this->CallPrefDictionary) as $condition) {
            $_prefName = $condition."_lastOther";
            $this->CallPrefLastOther[$condition]= $this->Preferences[$_prefName];
        }

        if (!count($conditions)) {
            $conditions=$this->CallPrefDictionary;
        }

        foreach (array_keys($conditions) as $condition) {

            $found++;
            $rr=floor($found/2);
            $mod=$found-$rr*2;

            $pref_name  = $conditions[$condition];
            $pref_value = $this->CallPrefDbURI[$condition];

            $select_name=$condition."_select";

            $set_uri_java="set_uri_" . $condition;
            $update_text_java="update_text_" . $condition;

            print "
            <tr valign=top>
              <td class=border valign=middle>$pref_name</td>
              <td class=border valign=middle align=left>
            ";

            $phoneValues = array();

            foreach ($this->divertTargets as $phones) {
                $phoneValues[] = $phones['value'];
            }

            $lastOther = $this->CallPrefLastOther[$condition];
            $otherIdx = count($this->divertTargets) - 1;

            $phoneValues[$otherIdx] = $lastOther;

            $targets = sprintf("'%s'", join("', '", $phoneValues));

            print "
            <SCRIPT>
            var ${condition}_other = '$lastOther';

            function $set_uri_java(elem) {
                var index;
                var targets = [$targets];

                index = elem.selectedIndex;
                if (index == $otherIdx) {
                    document.sipsettings.$condition.value=${condition}_other;
                    document.sipsettings.$condition.style.display = 'block';
                } else {
                    document.sipsettings.$condition.style.display = 'none';
                    document.sipsettings.$condition.value=targets[index];
                }
            }

            function $update_text_java(elem) {
                ${condition}_other = elem.value;
            }
            </SCRIPT>
            ";

            print "<table border=0 cellspacing=0 cellpadding=0>
            <tr><td>";

            print "<select name=$select_name onChange=$set_uri_java(this)>\n";

            if ($this->CallPrefDbURI[$condition]) {
                $this->CallPrefUriType[$condition]=='Other';
            } else {
                $this->CallPrefUriType[$condition]=='Disabled';
            }

            $foundSelected=0;
            $nr_targets=count($this->divertTargets);

            foreach ($this->divertTargets as $idx => $phone) {

                $name = $phone['name'];

                if ($this->access_numbers[$condition]) {
                    if ($phone['description'] == "Mobile") {
                        $name .= sprintf(' (Dial %s0)',$this->access_numbers[$condition]);
                    } else if ($phone['description'] == "Voicemail") {
                        $name .= sprintf(' (Dial %s1)',$this->access_numbers[$condition]);
                    } else if ($phone['description'] == "Disabled") {
                        $name .= sprintf(' (Dial %s)',$this->access_numbers[$condition]);
                    } else if ($phone['description'] == "Other") {
                        $name .= sprintf(' (Dial %s+ NUMBER)',$this->access_numbers[$condition]);
                    }
                }

                if ($phone['description'] == 'Other')
                    $value = $lastOther;
                else
                    $value = $phone['value'];

                if (!$foundSelected &&
                    ($this->CallPrefDbURI[$condition]==$phone['value'] || $idx==$nr_targets-1)) {
                    print "<option value=\"$idx\" selected>$name</option>\n";
                    $pref_value = $value;
                    $this->CallPrefUriType[$condition]=$phone['description'];
                    $foundSelected=1;
                } else {
                    print "<option value=\"$idx\">$name</option>\n";
                }
            }

            print "</select>";

            print "
            </td><td>";

            if ($this->CallPrefUriType[$condition]=='Other')
                $style = "visible";
            else
                $style = "hidden";

            $pref_value=$this->CallPrefDbURI[$condition];

            print "
                <span>
                <td>
                  <input class=$style type=text size=40
                         name=$condition value=\"$pref_value\"
                         onChange=$update_text_java(this)>
                </span>
                ";
            if ($condition=="FNOA") {
                print " ";
                print _("Timeout");
                print " ";

                $selected_timeout[$this->timeout]="selected";

                print "<select name=timeout>";
                foreach (array_keys($this->timeoutEls) as $_el) {
                    printf ("<option value=\"%s\" %s>%s",$_el,$selected_timeout[$_el],$this->timeoutEls[$_el]);
                }
                print "</select>";
            }

            if ($condition=="FUNV" && $this->FUNC_access_number) {
                printf ("Dial %s2*X where X = number of minutes, 0 to reset", $this->access_numbers['FUNC']);
            }

            print "
            </td>
            </tr>
            </table>";

            print "

              </td>
            </tr>
            ";
        }

    }

    function showHeader() {
        print "
        <table class=settings border=0 width=650>
        <tr>
        <td colspan=2 align=right>
        ";
        if ($this->logoFile) {
            print "<img src=./$this->logoFile border=0>";
            print "<p>";
        }

        print "
        </td>
        </tr>
        </table>
        ";
    }

    function chapterTableStart() {
        print "
        <table class=settings border=0 width=650>
        ";
    }

    function chapterTableStop() {
        print "
        </table>
        ";
    }



    function getENUMmappings () {
        dprint("getENUMmappings(engine=$this->enum_engine)");

		$this->enums=array();

        $filter=array(
                      'type'     => 'sip',
                      'mapto'    => $this->account,
                      'owner'    => $this->owner
                      );
        // Range
        $range=array('start' => 0,
                     'count' => 10
                     );

        // Order

        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        // Insert credetials
        $this->EnumPort->addHeader($this->SoapAuthEnum);
        $result = $this->EnumPort->getNumbers($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (EnumPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        foreach($result->numbers as $_number) {
            $enum='+'.$_number->id->number;
            $this->voicemailUsernameOptions[]=$enum;
            if (!in_array($enum,$this->enums)) $this->enums[]=$enum;
        }
    }

    function enum2tel($enum_text) {
        // transform enum style domain name in forward telephone number
    
        $enum_text=trim($enum_text);
    
        if (preg_match("/^\+\d+$/",$enum_text)) {
            return $enum_text;
        }
    
        $z=0;
        $tel_text="";
    
        while ($z < strlen($enum_text)) {
            $char = substr($enum_text,$z,1);
            if (preg_match("/[a-zA-Z]/",$char)) {
                break;
            } else if (preg_match("/[0-9]/",$char)) {
                $tel_text=$char.$tel_text;
                $z++;
            } else {
                $z++;
            }
        }
    
        if ($tel_text) {
            $tel_text="+".$tel_text;
            return ($tel_text);
        } else {
            return $enum_text;
        }
    }

    function showTimezones($name,$value) {
        if (!$fp = fopen("timezones", "r")) {
            print _("Failed to open timezone file.");
            return false;
        }
        printf ("<select name=%s>",$name);
        print "\n<option>";
        while ($buffer = fgets($fp,1024)) {
            $buffer=trim($buffer);
            if ($value==$buffer) {
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

    function showQuickDial() {
        if (!preg_match("/^\d+$/",$this->username)) return 1;
        print "
        <tr>
          <td class=border>";
            print _("Quick dial");
            print "
          </td>
          <td class=border align=left>
            <input type=text size=15 maxsize=64 name=quickdial value=\"$this->quickdial\">
            ";
            if ($this->quickdial && preg_match("/^$this->quickdial/",$this->username)) {
                $dial_suffix=strlen($this->username) - strlen($this->quickdial);
                if ($dial_suffix > 0) {
                    printf (_("Prefix to auto-complete short numbers"),$dial_suffix);
                }
            }
            print "
          </td>
        </tr>
        ";
    }


    function showMobileNumber() {

           if ($this->SOAPversion <= 1) return;
        if (!in_array("free-pstn",$this->groups)) return;

        print "
        <tr>
          <td class=border>";
            print _("Mobile number");
            printf ("
          </td>
          <td class=border align=left>
            <input type=text size=15 maxsize=64 name=mobile_number value='%s'>
          </td>
        </tr>
        ",$this->Preferences['mobile_number']);

    }

    function showLastCalls() {

        $this->getCalls();

        if ($this->calls) {
            $chapter=sprintf(_("Call statistics"));
            $this->showChapter($chapter);

            $calltime=normalizeTime($this->duration);
    
            print "
            <tr>
              <td class=border>";
                if ($this->cdrtool_address) {
                    print "<a href=$this->cdrtool_address target=cdrtool>";
                    print _("Summary");
                    print "</a>";
                } else {
                    print _("Usage data");
                }
                print "
              </td>
              <td class=border>";
              printf (_("%s calls /  %s /  %s / %.2f %s"), $this->calls, $calltime,$this->trafficPrint,$this->price,$this->currency);
              print "

              </td>
            </tr>
            ";
    
            print "
            <tr>
              <td class=border>";
                print _("First / Last call");
                print "
              </td>
              <td class=border>
                $this->firstCall / $this->lastCall
              </td>
            </tr>
            ";
        }

        if (count($this->calls_received)) {
            $chapter=sprintf(_("Last received calls"));
            $this->showChapter($chapter);

            $j=0;
            foreach (array_keys($this->calls_received) as $call) {
                $j++;

                $uri         = $this->calls_received[$call][from];
                $duration      = normalizeTime($this->calls_received[$call][duration]);
                $dialURI     = $this->PhoneDialURL($uri) ;
                $htmlDate    = $this->colorizeDate($this->calls_received[$call][date]);
                $htmlURI     = $this->htmlURI($uri);
                $urlURI         = urlencode($this->normalizeURI($uri));

                if (!$this->calls_received[$call][duration]) {
                    $htmlURI = "<font color=red>$htmlURI</font>";
                }

                print "
                <tr>
                <td class=border>$htmlDate</td>
                <td class=border>
                <table border=0 width=100% cellspacing=0 cellpadding=0>
                <tr>
                <td align=left width=10>$dialURI</td>
                <td align=right width=40>$duration</td>
                <td align=right width=10></td>
                <td align=left><nobr>$htmlURI</nobr></td>
                ";
                if ($this->Preferences['advanced']) {
                    print "<td align=right><a href=$this->pageURL&tab=phonebook&task=add&uri=$urlURI&search_text=$urlURI>$this->phonebook_img</a></td>";
                }
                print "
                </tr>
                </table>";
                print "</td>
                </tr>
                ";
            }
        }

        if (count($this->calls_placed)) {
            $chapter=sprintf(_("Last placed calls"));
            $this->showChapter($chapter);

            $j=0;

            foreach (array_keys($this->calls_placed) as $call) {
                $j++;

                if ($this->calls_placed[$call]['to'] == "sip:".$this->voicemail['Account'] ) {
                    continue;
                }

                $uri         = $this->calls_placed[$call]['to'];
                $price       = $this->calls_placed[$call]['price'];
                $status      = $this->calls_placed[$call]['status'];
                $rateinfo    = $this->calls_placed[$call]['rateInfo'];
                $duration    = normalizeTime($this->calls_placed[$call]['duration']);
                $dialURI     = $this->PhoneDialURL($uri) ;
                $htmlDate    = $this->colorizeDate($this->calls_placed[$call]['date']);
                $htmlURI     = $this->htmlURI($uri);
                $urlURI         = urlencode($this->normalizeURI($uri));

                if ($price) {
                    $price_print =sprintf(" (%s %s)",$price,$this->currency);
                } else {
                    $price_print = '';
                }
                print "
                <tr>
                <td class=border>$htmlDate</td>
                <td class=border>
                <table border=0 width=100% cellspacing=0 cellpadding=0>
                <tr>
                <td align=left width=10>$dialURI</td>
                <td align=right width=40>$duration</td>
                <td align=right width=10></td>
                <td align=left><nobr>$htmlURI $price_print</nobr></td>
                ";

                if ($this->Preferences['advanced']) {
                    print "<td align=right><a href=$this->pageURL&tab=phonebook&task=add&uri=$urlURI&search_text=$urlURI>$this->phonebook_img</a></td>";
                }
                print "
                </tr>
                </table>";
                print "</td>
                </tr>
                ";
            }
        }

    }

    function getCalls () {
        dprint("getCalls()");

        $fromDate=time()-3600*24*60; // last two months
        $toDate=time();

        $CallQuery=array("fromDate"=>$fromDate,
                         "toDate"=>$toDate,
                         "limit"=>30
                         );

        $CallsQuery=array("placed"=>$CallQuery,
                          "received"=>$CallQuery
                          );

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getCalls($this->sipId,$CallsQuery);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        // received  calls
        foreach ($result->received as $callStructure) {
            $this->calls_received[]=array(
                                    "from"     => quoted_printable_decode($callStructure->fromURI),
                                    "duration" => $callStructure->duration,
                                    "status"   => $callStructure->status,
                                    "date"     => getLocalTime($this->timezone,$callStructure->startTime)
                                     );         
        }

        // placed calls
        foreach ($result->placed as $callStructure) {

            if ($callStructure->status == 435) continue;

            $this->calls_placed[]=array(
                                    "to"       => quoted_printable_decode($callStructure->toURI),
                                    "duration" => $callStructure->duration,
                                    "price"    => $callStructure->price,
                                    "rate"     => $callStructure->rate,
                                    "status"   => $callStructure->status,
                                    "date"     => getLocalTime($this->timezone,$callStructure->startTime)
                                     );         
        }

    }

    function getCallStatistics () {
        dprint("getCallStatistics");

        $fromDate=mktime(0, 0, 0, date("m"), "01", date("Y"));
        $toDate=time();

        $CallQuery=array("fromDate"=>$fromDate,
                         "toDate"=>$toDate
                         );

        $CallQuery=array("limit"=>1
                         );

        $CallsQuery=array("placed"=>$CallQuery);

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getCallStatistics($this->sipId,$CallsQuery);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        //dprint_r($result);

        $this->thisMonth['calls'] = $result->placed->calls;
        $this->thisMonth['price'] = $result->placed->price;
    }

    function addPhonebookEntry() {
        dprint("addPhonebookEntry()");

        $uri       = strtolower(trim($_REQUEST['uri']));

        if (!strlen($uri)) return false;

        $phonebookEntry=array('uri'       => $uri);

        dprint("addPhonebookEntry");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->addPhoneBookEntry($this->sipId,$phonebookEntry);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }


        return true;
    }

    function updatePhonebookEntry() {

        dprint("updatePhonebookEntry()");
        $uri       = strtolower(trim($_REQUEST['uri']));
        $group     = trim($_REQUEST['group']);

        if ($this->SOAPversion > 1) {
            $name = trim($_REQUEST['name']);
            $phonebookEntry=array('name' => $name,
                                  'uri'       => $uri,
                                  'group'     => $group
                              );
        } else {
            $firstName = trim($_REQUEST['first_name']);
            $lastName  = trim($_REQUEST['last_name']);
            $phonebookEntry=array('firstName' => $firstName,
                                  'lastName'  => $lastName,
                                  'uri'       => $uri,
                                  'group'     => $group
                              );
        }

        //dprint_r($phonebookEntry);

        dprint("updatePhonebookEntry");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->updatePhoneBookEntry($this->sipId,$phonebookEntry);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;

    }

    function deletePhonebookEntry() {
        dprint("deletePhonebookEntry()");

        $uri = strtolower($_REQUEST['uri']);

        dprint("deletePhonebookEntry");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->deletePhoneBookEntry($this->sipId,$uri);
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;
    }

    function getPhoneBookEntries() {
        dprint("getPhoneBookEntries()");
        $search_text = trim($_REQUEST['search_text']);
        $group       = trim($_REQUEST['group']);

        if (!strlen($search_text)) $search_text="%" ;

        if ($this->SOAPversion > 1) {
            $match=array('uri'  => $search_text,
                         'name' => $search_text
                         );
        } else {
            $match=array('uri'       => $search_text,
                         'firstName' => $search_text,
                         'lastName'  => $search_text
                         );
        }

        if (strlen($group)) {
            if ($group=="empty") {
                $match['group']='';
            } else {
                $match['group']=$group;
            }
        }

        $range=array('start'=>0,
                     'count'=>100);

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getPhoneBookEntries($this->sipId,$match,$range);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        $this->PhonebookEntries=$result->entries;
        //dprint_r($this->PhonebookEntries);
    }

    function showPhonebook() {
        dprint("showPhonebook()");

        $chapter=sprintf(_("Contacts"));
        $this->showChapter($chapter);

        if (!$this->Preferences['advanced']) {
            return ;
        }

        print "
        <tr>
        <td class=border colspan=2 align=left>";
        print _("You can organize, export and group your contacts. ");
        print _("Groups can be used to Accept selected incoming calls. ");
        print "
        </td>
        </tr>
        ";


        print '
        <SCRIPT>
            function toggleVisibility(rowid) {
                if (document.getElementById) {
                    row = document.getElementById(rowid);
                    if (row.style.display == "block") {
                        row.style.display = "none";
                    } else {
                        row.style.display = "block";
                    }
                    return false;
                } else {
                    return true;
                }
            }
        </SCRIPT>
        ';

        print "<tr><td colspan=2>";

        $adminonly = $_REQUEST['adminonly'];
        $search_text = $_REQUEST['search_text'];
        $accept = $_REQUEST['accept']; // selected search group;

        $task = $_REQUEST['task'];
        $confirm = $_REQUEST['confirm'];

        $group = $_REQUEST['group'];
        $uri = $_REQUEST['uri'];

        if ($this->SOAPversion > 1) {
            $name = $_REQUEST['name'];
        } else {
            $first_name = $_REQUEST['first_name'];
            $last_name = $_REQUEST['last_name'];
        }

        if ($task=="deleteContact" && $confirm) {
            $this->deletePhonebookEntry();
            unset($task);
            unset($confirm);
        } else if ($task=="update") {
            $this->updatePhonebookEntry();
            unset($task);
        } else if ($task=="add") {
            $this->addPhonebookEntry();
            unset($task);
        }

        $this->getPhoneBookEntries();
        $maxrowsperpage=250;
    
        $url_string=$this->pageURL."&tab=phonebook";

        print "
        <p>
        <table width=100% cellpadding=1 cellspacing=1 border=0>
        <tr>
        <form action=$this->pageURL method=post>
        <input type=hidden name=tab value=phonebook>
        <input type=hidden name=task value=add>
        <td class=h align=left valign=top>
        <input type=submit value=";
        print _("Add");
        print ">
        <input type=text size=20 name=uri>
        ";
        print _("(wildcard %)");
        print "
        </td>
        </form>
        <form action=$this->pageURL method=post>
        <input type=hidden name=tab value=phonebook>
        <td class=h align=right valign=top>
        <input type=text size=20 name=search_text value=\"$search_text\">
        ";

        $selected[$group]="selected";

        print "<select name=group>";
        print "<option value=\"\">";
        foreach(array_keys($this->PhonebookGroups) as $key) {
            printf ("<option value=\"%s\" %s>%s",$key,$selected[$key],$this->PhonebookGroups[$key]);
        }
        print "<option value=\"\">------";
        printf ("<option value=\"empty\" %s>No group",$selected['empty']);

        print "</select>";

        print "<input type=submit value=";
        print _("Search");
        print ">";
        print "
        <a href=$this->pageURL&tab=phonebook&export=1 target=export>";
        print _("Export");
        print "</a>
        </td>
        </form>
        </tr>
        </table>
        ";

        if (count($this->PhonebookEntries)){
            print "
            <p>
            <table width=100% cellpadding=1 cellspacing=1 border=0 bgcolor=lightgrey>
            <tr bgcolor=#CCCCCC>
            <td class=h align=right>Id</td>
            ";
            print "<td class=h>";
            print _("Address");
            print "</td>";
            print "<td class=h>";
            print "</td>";
            print "<td class=h>";
            print _("Name");
            print "</td>";
            print "<td class=h>";
            print _("Group");
            print "</td>";
            print "<td class=h>";
            print _("Action");
            print "</td>";
            print "</tr>";
    
            foreach(array_keys($this->PhonebookEntries) as $_entry) {
        
                $found=$i+1;
    
                print "
                <tr bgcolor=white valign=top>
                <form name=\"Entry$found\" action=\"$this->pageURL&tab=$this->tab\">
                $this->hiddenElements
                <input type=hidden name=tab value=\"$this->tab\">
                <input type=hidden name=task value=\"update\">
                ";
                printf ("<input type=hidden name=uri value=\"%s\">",$this->PhonebookEntries[$_entry]->uri);

                print "<td valign=top>$found</td>
                <td>";
                print $this->PhonebookEntries[$_entry]->uri;

                printf ("</td>
                <td valign=top>%s</td>
                <td valign=top>",
                $this->PhoneDialURL($this->PhonebookEntries[$_entry]->uri));

                if ($this->SOAPversion > 1) {
                    print _("Name");
                    printf ("<input type=text name=name value='%s'>",$this->PhonebookEntries[$_entry]->name);
                    print "<a href=\"javascript: document.Entry$found.submit()\">Update</a>";
                } else {
                    $fname    = $this->PhonebookEntries[$_entry]->firstName;
                    $lname    = $this->PhonebookEntries[$_entry]->lastName;

                    if ($fname || $lname) {
                        print "<a onClick=\"return toggleVisibility('row$found')\" href=#>$fname $lname</a>";
                    } else {
                        print "<a onClick=\"return toggleVisibility('row$found')\" href=#>Edit</a>";
                    }

                    print "
                    <table border=0 class=extrainfo id=row$found cellpadding=0 cellspacing=0 width=100%>
                    <tr>
                    <td>";
                    print _("First");
                    print "</td>";
                    print "<td><input type=text name=first_name value=\"$fname\"></td>
                    </tr>
                    <tr>
                    <td>
                    ";
                    print _("Last");
                    print "</td>";
                    print "<td><input type=text name=last_name value=\"$lname\"></td>";
                    print "</tr>";

                    print "<tr><td><input type=submit value=Save></td></tr>";
                    print "
                    </table>
                    ";
                }

                print "
                </td>
                <td valign=top>";
                if ($this->SOAPversion > 1) {
                    printf ("<select name=group onChange=\"location.href='%s&task=update&uri=%s&name=%s&group='+this.options[this.selectedIndex].value\">",$url_string,urlencode($this->PhonebookEntries[$_entry]->uri),urlencode($this->PhonebookEntries[$_entry]->name));
                } else {
                    printf ("<select name=group onChange=\"location.href='%s&task=update&uri=%s&first_name=%s&last_name=%s&group='+this.options[this.selectedIndex].value\">",$url_string,urlencode($this->PhonebookEntries[$_entry]->uri),urlencode($fname),urlencode($lname));
                }

                print "<option value=\"\">";
                $selected_grp[$this->PhonebookEntries[$_entry]->group]="selected";
                foreach(array_keys($this->PhonebookGroups) as $_key) {
                    printf ("<option value=\"%s\" %s>%s",$_key,$selected_grp[$_key],$this->PhonebookGroups[$_key]);
                }
                unset($selected_grp);

                print "</select>";


                print "</td>
                ";

                if ($task=="deleteContact" && $uri==$this->PhonebookEntries[$_entry]->uri) {
                    print "
                    <td bgcolor=red valign=top>
                    ";
                    printf ("<a href=%s&task=deleteContact&uri=%s&confirm=1>",$url_string,urlencode($this->PhonebookEntries[$_entry]->uri));
                    print _("Confirm");
                } else {
                    print "
                    <td valign=top>";
                    printf ("<a href=%s&task=deleteContact&uri=%s>",$url_string,urlencode($this->PhonebookEntries[$_entry]->uri));
                    if ($this->delete_img) {
                        print $this->delete_img;
                    } else {
                        print _("Delete");
                    }
                }
                print "</a>";
                print "</td>
                </form>
                </tr>";
                $i++;
    
            }
    
            print "</table>";
            print "
            <p>
            <center>
            <table border=0>
            <tr>
            <td>
            ";

            print "
            </td>
            </tr>
            </table>
            ";
        }
    }

    function exportPhonebook($userAgent) {
        dprint("exportPhonebook()");
        $this->getPhonebookEntries();

        $this->contentType="Content-type: text/csv";

        if (!is_array($this->PhonebookEntries) || !count($this->PhonebookEntries)) return true;

        if (!$userAgent) $userAgent='snom';

        if ($userAgent=='snom') {
            $this->exportFilename="tbook.csv";
            $phonebook.=sprintf("Name,Address,Group\n");
        } else if ($userAgent == 'eyebeam') {
            $phonebook.=sprintf("Name,Group Name,SIP URL,Proxy ID\n");
        } else if ($userAgent == 'csco') {
            $this->contentType="Content-type: text/xml";
            $this->exportFilename="directory.xml";
            $phonebook.=sprintf ("<CiscoIPPhoneDirectory>\n\t<Title>%s</Title>\n\t<Prompt>Directory</Prompt>\n",$this->account);
        } else if ($userAgent == 'unidata') {
            $this->exportFilename="phonebook.csv";
            $phonebook.=sprintf("Index,Name,,,,\n");
            $phonebook.=sprintf("0,Undefined,,,,\n");

            $z=1;
            foreach($this->PhonebookGroups as $_group) {
                $this->groupIndex[$_group]=$z;
                $phonebook.=sprintf ("%s,%s,,,,\n",$z,$_group);
                $z++;
            }

            $phonebook.=sprintf("\nIndex,Name,RdNm,Tel,Group\n");

        }

        $found=0;

        foreach (array_keys($this->PhonebookEntries) as $_entry) {
    
            $fname    = $this->PhonebookEntries[$_entry]->firstName;
            $lname    = $this->PhonebookEntries[$_entry]->lastName;
            $uri      = $this->PhonebookEntries[$_entry]->uri;
            $group    = $this->PhonebookEntries[$_entry]->group;

            if (!preg_match("/[_%]/",$uri)) {
                $uri=substr($uri,4);
                $els=explode("@",$uri);
                if ($els[1]==$this->domain) $uri=$els[0];
                if ($userAgent=='snom') {
                    $phonebook.=sprintf ("%s %s,%s,%s\n",$fname,$lname,$uri,$this->PhonebookGroups[$group]);
                } else if ($userAgent == 'unidata' && $fname && $lname) {
                    $phonebook.=sprintf ("%s,%s,%s %s,%s,%s\n",$found,$fname,$fname,$lname,$uri,$this->PhonebookGroups[$group]);
                } else if ($userAgent == 'eyebeam') {
                    $phonebook.=sprintf ("%s %s,%s,1\n",$fname,$lname,$this->PhonebookEntries[$_entry]->uri,$this->PhonebookGroups[$group]);
                } else if ($userAgent == 'csco') {
                    $phonebook.=sprintf ("\n\t<DirectoryEntry>\n\t<Name>%s %s</Name>\n\t<Telephone>%s</Telephone>\n\t</DirectoryEntry>\n",$fname,$lname,$uri);
                }
                $found++ ;
            }
        }

        if ($userAgent == 'csco') {
            $phonebook.=sprintf ("\n</CiscoIPPhoneDirectory>\n");
        }

        Header($this->contentType);
        $_header=sprintf("Content-Disposition: inline; filename=%s",$this->exportFilename);
        Header($_header);
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
        header("Pragma: no-cache");

        print $phonebook;

    }

    function getRejectMembers() {
        dprint("getRejectMembers()");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getRejectMembers($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        $this->rejectMembers=$result;
        dprint_r($this->rejectMembers);

        return true;
    }

    function setRejectMembers() {

        $members=array();

        $rejectMembers=$_REQUEST['rejectMembers'];

        foreach ($rejectMembers as $_member) {
            if (strlen($_member) && !preg_match("/^sip:/",$_member)) {
                $_member = 'sip:'.$_member;
            }
            if (strlen($_member)) $members[]=$_member;
        }

        dprint("setRejectMembers");

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->setRejectMembers($this->sipId,$members);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }
    }

    function showRejectMembers() {
        $chapter=sprintf(_("Rejected callers"));
        $this->showChapter($chapter);

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";
        print "
        <tr>
        <td class=border colspan=2 align=left>";
        print _("Use %Number@% to match PSTN callers and user@domain to match SIP callers");
        print "
        </td>
        </tr>
        ";

        print "<tr>";
        print "<td class=border align=left>";
        print _("SIP caller");
        print "</td>";
        print "<td class=border align=left>
        <input type=text size=35 name=rejectMembers[]>
        </td>";
        print "<tr>";

        if ($this->getRejectMembers()) {
            foreach ($this->rejectMembers as $_member) {
                print "<tr>";
                print "<td class=border align=left>";
                print _("SIP caller");
                print "</td>";
                print "<td class=border align=left>
                <input type=text size=35 name=rejectMembers[] value=\"$_member\">
                </td>";
                print "<tr>";
            }
        }

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set reject members\">
        ";

        print "
        <input type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";

        print "
          </td>
          <td align=right>
          </td>
        </tr>
        ";

        print $this->hiddenElements;

        print "
        </form>
        ";

    }

    function getAcceptRules() {

        dprint("getAcceptRules()");

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getAcceptRules($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        foreach(array_keys($result->rules->persistent) as $_rule) {
            $_key=$result->rules->persistent[$_rule]->days;
            $this->acceptRules['persistent'][$_key]=array('start' =>$result->rules->persistent[$_rule]->start,
                                                          'stop'  =>$result->rules->persistent[$_rule]->stop,
                                                          'groups'=>$result->rules->persistent[$_rule]->groups);
        }

        $this->acceptRules['temporary']=array('groups'  => $result->rules->temporary->groups,
                                              'duration'=> $result->rules->temporary->duration
                                              );

        $this->acceptRules['groups']     = $result->nonEmptyGroups;

        //dprint_r($this->acceptRules);

        return true;
    }

    function setAcceptRules() {
        dprint("setAcceptRules()");
        //dprint_r($_REQUEST);

        $persistentAcceptArray=array();
        $temporaryAcceptArray=array();

        foreach (array_keys($this->acceptDailyProfiles) as $profile) {

            unset($groups);

            $radio_persistentVarName='radio_persistent_'.$profile;
            $radio_persistent=$_REQUEST[$radio_persistentVarName];

            if ($radio_persistent=="0") {
                $groups[]='everybody';
            } else if ($radio_persistent=="1") {
                $groups[]='nobody';
            } else if ($radio_persistent=="2") {
                $groupsVarName='groups_'.$profile;
                $groups=$_REQUEST[$groupsVarName];
            }

            $startVarName='start_'.$profile;
            $start=$_REQUEST[$startVarName];

            $stopVarName='stop_'.$profile;
            $stop=$_REQUEST[$stopVarName];

            if (!preg_match("/^[0-2][0-9]:[0-5][0-9]$/",$start) ||
                !preg_match("/^[0-2][0-9]:[0-5][0-9]$/",$stop) ||
                ($start=="00:00" &&  $stop=="00:00") ||
                !$start ||
                !$stop  ||
                ($radio_persistent=="2" && (!is_array($groups) || !count($groups) )) ) {

                continue;
            }

            $persistentAcceptArray[]=array('start'  => $start,
                                           'stop'   => $stop,
                                           'groups' => $groups,
                                           'days'   => intval($profile)
                                           );

        }

        // temporary
        $radio_temporary=$_REQUEST['radio_temporary'];

        unset($groups_temporary);

            if ($radio_temporary=="0") {
                $groups_temporary[]='everybody';
            } else if ($radio_temporary=="1") {
                $groups_temporary[]='nobody';
            } else if ($radio_temporary=="2") {
                $groups_temporary=$_REQUEST['groups_temporary'];
            }

        if (!is_array($groups_temporary)) $groups_temporary=array();

        $duration=$_REQUEST['duration'];

        $temporaryAccept=array("groups"  => $groups_temporary,
                               "duration"=> intval($duration)
                               );

        // combine persistent and temporary

        $rules=array("persistent" =>$persistentAcceptArray,
                     "temporary"  =>$temporaryAccept);

        //dprint_r($rules);

        dprint("setAcceptRules");

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->setAcceptRules($this->sipId,$rules);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;

    }

    function showAcceptRules() {
        $chapter=sprintf(_("Accept rules"));
        $this->showChapter($chapter);

        $this->getAcceptRules();

        $this->getVoicemail();
        $this->getDivertTargets();
        $this->getDiversions();

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        print "
        <tr>
        <td class=border colspan=2 align=left>";
        print _("You can use these features to accept or reject calls depending on the time of day and caller id. ");
        print _("You can create custom groups in the Contacts page like Family or Coworkers. ");
        print _("Rejected calls are diverted based on the Unavailable condition in the settings page. ");
        print "<p>";
        print "<p class=desc>";
        printf (_("Your current timezone is: %s"),$this->timezone);
        $timestamp=time();
        $LocalTime=getLocalTime($this->timezone,$timestamp);
        print " $LocalTime";

        print "
        </td>
        </tr>
        ";

        print "
        <tr>
        <td class=ag1 colspan=2 bgcolor=white align=left>";
        print _("If Unavailable");
        print " ";
        print _("divert calls to: ");

        foreach ($this->divertTargets as $idx => $phone) {
            //dprint_r($phone);
            if ($this->CallPrefDbURI['FUNV'] == "<voice-mailbox>") {
                $this->CallPrefDbURI['FUNV'] = $this->voicemail['Account'];
            }

            if ($this->CallPrefDbURI['FUNV']==$phone['value']) {
                printf ($phone['name']);
                break;
            }
        }

        print "
        </td>
        </tr>
        ";

        $chapter=sprintf(_("Temporary rule"));
        $this->showChapter($chapter);

        print "
        <tr>
        <td class=border colspan=2 align=left>";
        print _("This will override the permanent rules for the chosen duration. ");
        print "
        </td>
        </tr>
        ";

        print "<tr>";
        print "<td colspan=2 class=border>";

        print "<table border=0 width=100%>
            <tr>
            <td>
            ";
        print _("Duration:");

        if ($this->acceptRules['temporary']['duration']) {
            printf ('
            <script LANGUAGE="JavaScript">
                var minutes = %s;
                ID=window.setTimeout("update();", 1000*60);
                function update() {
                        minutes--;
                        document.sipsettings.minutes.value = minutes;
                        ID=window.setTimeout("update();",1000*60);
                 }

            </script>
            ',$this->acceptRules['temporary']['duration']);

                print " <font color=red>";
                print " <input type=text name=minutes size=3 maxsize=3 value=\"";
                print $this->acceptRules['temporary']['duration'];
                print "\" disabled=true>";
                print " <input type=hidden name=accept_temporary_remain value=\"";
                print $this->acceptRules['temporary']['duration'];
                print "\"> ";
                print "</font>";
        } else {
                print "<select name=duration> ";
                print "<option>";
                print "<option value=1  >  1";
                print "<option value=5  >  5";
                print "<option value=10 > 10";
                print "<option value=20 > 20";
                print "<option value=30 > 30";
                print "<option value=45 > 45";
                print "<option value=60 > 60";
                print "<option value=90 > 90";
                print "<option value=120>120";
                print "<option value=150>150";
                print "<option value=180>180";
                print "<option value=240>240";
                print "<option value=480>480";
                print "</select> ";
        }

        print _("minute(s)");

        $_name="radio_temporary";

        $_checked_everybody="";
        $_checked_nobody="";
        $_checked_groups="";

        print "<td>";
        if (is_array($this->acceptRules['temporary']['groups']) &&in_array("everybody",$this->acceptRules['temporary']['groups'])) {
            $_checked_everybody="checked";
        } else if (is_array($this->acceptRules['temporary']['groups']) && in_array("nobody",$this->acceptRules['temporary']['groups'])) {
            $_checked_nobody="checked";
        } else if (!in_array('everybody',$this->acceptRules['temporary']['groups']) &&
                   !in_array('nobody',$this->acceptRules['temporary']['groups']) &&
                   count($this->acceptRules['temporary']['groups'])) {
            $_checked_groups="checked";
        }

        if ($_checked_nobody) {
            $class_nobody="ag1";
        } else {
            $class_nobody="note";
        }

        printf ("<td class=note><input type=radio name=%s value=0 %s>%s",$_name,$_checked_everybody,_("Everybody"));
        printf ("<td class=$class_nobody><input type=radio name=%s value=1 %s>%s",$_name,$_checked_nobody,_("Nobody"));

        $c=count($this->acceptRules['groups']);

        if ($_checked_groups) {
            $class_groups="ag1";
        } else {
            $class_groups="note";
        }

        print "<td class=$class_groups>";
        //dprint_r($this->acceptRules['groups']);

        if (count($this->acceptRules['groups'])>2) {

            printf ("<input type=radio name=%s value=2 %s class=hidden>",$_name,$_checked_groups);
            $i=0;

            foreach(array_keys($this->acceptRules['groups']) as $_group) {
                $i++;

                if (preg_match("/(everybody|nobody)/",$this->acceptRules['groups'][$_group])) continue;

                if (in_array($this->acceptRules['groups'][$_group],$this->acceptRules['temporary']['groups'])) {
                    $_checked="checked";
                } else {
                    $_checked="";
                }

                $_name="groups_temporary[]";
                printf ("<input type=checkbox name=%s value=%s onClick=\"document.sipsettings.radio_temporary[2].checked=true\" %s>%s ",
                $_name,
                $this->acceptRules['groups'][$_group],
                $_checked,
                $this->PhonebookGroups[$this->acceptRules['groups'][$_group]]
                );
            }
        }

        print "
        </tr>
        </table>
        ";

        print "
        </td>
        </tr>

        ";

        $chapter=sprintf(_("Permanent rules"));
        $this->showChapter($chapter);

        print "
        <tr valign=top>
        <td colspan=2 class=border align=left valign=top>
        ";

        print "<table border=0 width=100%>";
        print "<tr bgcolor=lightgrey>
        <td>";
        print _("Days");
        print "</td>
        <td colspan=2>";
        print _("Time interval");
        print "</td>
        <td colspan=3>";
        print _("Groups");
        print "</td>
        </tr>
        ";

        foreach (array_keys($this->acceptDailyProfiles) as $profile) {

            if ($this->acceptRules['persistent'][$profile]['start'] || $this->acceptRules['persistent'][$profile]['stop']) {
                $class="ag1";
            } else {
                $class="mhj";
            }
            if ($profile==1) {
                print "<tr><td colspan=6 heigth=5 bgcolor=lightgrey></td></tr>";
            }

            print "
            <tr>
            <td valign=top class=$class>";

            printf ("%s",$this->acceptDailyProfiles[$profile]);
            unset($selected_StartTime);
            $selected_StartTime[$this->acceptRules['persistent'][$profile]['start']]="selected";
            printf ("<td valign=top><select name=start_%s>",$profile);

            $t=0;
            $j=0;

            print "<option>";

            while ($t<24) {
                if (!$j) {
                    if (strlen($t)==1) {
                        $t1="0".$t.":00";
                    } else {
                        $t1=$t.":00";
                    }
                    $j++;
                } else {
                    if (strlen($t)==1) {
                        $t1="0".$t.":30";
                    } else {
                        $t1=$t.":30";
                    }
                    $j=0;
                    $t++;
                }
                print "<option $selected_StartTime[$t1]>$t1";
            }

            printf ("<option %s>23:59",$selected_StartTime['23:59']);
            print "</select>";

            unset($selected_StopTime);

            $selected_StopTime[$this->acceptRules['persistent'][$profile]['stop']]="selected";
            printf ("<td valign=top><select name=stop_%s>",$profile);
            $t=0;
            $j=0;
            print "<option>";
            while ($t<24) {
                if (!$j) {
                    if (strlen($t)==1) {
                        $t1="0".$t.":00";
                    } else {
                        $t1=$t.":00";
                    }
                    $j++;
                } else {
                    if (strlen($t)==1) {
                        $t1="0".$t.":30";
                    } else {
                        $t1=$t.":30";
                    }
                    $j=0;
                    $t++;
                }
                print "<option $selected_StopTime[$t1]> $t1";
            }
            printf ("<option %s>23:59",$selected_StopTime['23:59']);
            print "</select>";

            $_name="radio_persistent_".$profile;

            $_checked_everybody="";
            $_checked_nobody="";
            $_checked_groups="";

            if (is_array($this->acceptRules['persistent'][$profile]['groups']) && in_array("everybody",$this->acceptRules['persistent'][$profile]['groups'])) {
                $_checked_everybody="checked";
            } else if (is_array($this->acceptRules['persistent'][$profile]['groups']) && in_array("nobody",$this->acceptRules['persistent'][$profile]['groups'])) {
                $_checked_nobody="checked";
            } else if (!in_array('everybody',$this->acceptRules['persistent'][$profile]['groups']) &&
                       !in_array('nobody',$this->acceptRules['persistent'][$profile]['groups']) &&
                       count($this->acceptRules['persistent'][$profile]['groups'])) {
                $_checked_groups="checked";
            } else {
                $_checked_everybody="checked";
            }

            if ($_checked_nobody) {
                $class_nobody="ag1";
            } else {
                $class_nobody="note";
            }

            printf ("<td class=note><input type=radio name=%s value=0 %s>%s",$_name,$_checked_everybody,_("Everybody"));
            printf ("<td class=$class_nobody><input type=radio name=%s value=1 %s>%s",$_name,$_checked_nobody,_("Nobody"));

            $c=count($this->acceptRules['groups']);

            if ($_checked_groups) {
                $class_groups="ag1";
            } else {
                $class_groups="note";
            }

            print "<td class=$class_groups>";
            if (count($this->acceptRules['groups'])>2) {

                printf ("<input type=radio name=%s value=2 %s class=hidden>",$_name,$_checked_groups);
                $i=0;
    
                foreach(array_keys($this->acceptRules['groups']) as $_group) {
                    $i++;
                    if (preg_match("/(everybody|nobody)/",$this->acceptRules['groups'][$_group])) continue;

                    if (in_array($this->acceptRules['groups'][$_group],$this->acceptRules['persistent'][$profile]['groups'])) {
                        $_checked="checked";
                    } else {
                        $_checked="";
                    }
    
                    $_name="groups_".$profile."[]";
                    printf ("<input type=checkbox name=%s value=%s onClick=\"document.sipsettings.radio_persistent_%s[2].checked=true\" %s>%s ",
                    $_name,
                    $this->acceptRules['groups'][$_group],
                    $profile,
                    $_checked,
                    $this->PhonebookGroups[$this->acceptRules['groups'][$_group]]
                    );
                }
            }

            print "
            </tr>
            ";
        }

        print "</table>";
        print "</td>
        </tr>";

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set accept rules\">
        ";

        print "
        <input type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";

        print "
          </td>
          <td align=right>
          </td>
        </tr>
        ";

        print $this->hiddenElements;
        print "
        </form>
        ";

    }

    function sendEmail($skip_html=False) {
        dprint ("SipSettings->sendEmail($this->email)");

        $this->getVoicemail();
        $this->getENUMmappings();
        $this->getAliases();

        $this->countAliases=count($this->aliases);

        if (!$this->email && !$skip_html) {
            print "<p><font color=blue>";
            print _("Please fill in the e-mail address.");
            print "</font>";
            return false;
        }

        $subject = "SIP account settings of $this->name";

        $tpl = $this->getEmailTemplate($this->reseller, $this->Preferences['language']);

	    if (!$tpl && !$skip_html) {
            print "<p><font color=red>";
        	print _("Error: no email template found");
            print "</font>";
            return false;
        }

        if (in_array("free-pstn",$this->groups)) $this->allowPSTN=1; // used by smarty

        define("SMARTY_DIR", "/usr/share/php/smarty/libs/");
        include_once(SMARTY_DIR . 'Smarty.class.php');

        $smarty = new Smarty;
        $smarty->template_dir = '.';

        //$smarty->use_sub_dirs = true;
        //$smarty->cache_dir = 'templates_c';

        $smarty->assign('client', $this);
        $body = $smarty->fetch($tpl);

        if (mail($this->email, $subject, $body, "From: $this->support_email") && !$skip_html) {
            print "<p>";
            printf (_("SIP settings have been sent to %s"), $this->email);
        }
    }

    function checkSettings() {
        dprint ("checkSettings()");

        foreach ($this->WEBdictionary as $el) {
            ${$el}=trim($_REQUEST[$el]);
        }

        if ($accept_temporary_remain && !is_numeric($accept_temporary_remain)) {
            $this->error=_("Invalid expiration period. ");
            return false;
        }

        if ($quota && !is_numeric($quota) && !is_float($quota)) {
            $this->error=_("Invalid quota. ");
            return false;
        }

        if (!$timezone && !$this->timezone) {
            $this->error=_("Missing time zone. ");
            return false;
        }

        if (!$this->checkEmail($mailto)) {
            $this->error=_("Invalid e-mail address. ");
            return false;
        }

        $rpid=preg_replace("/[^0-9\x]/","",$rpid);

        if (preg_match("/^0+([1-9]\d*)$/",$rpid,$m)) $rpid=$m[1];

        $quickdial=preg_replace("/[^0-9]/","",$quickdial);

        if (!strlen($accept_temporary_group)) $accept_temporary_remain=0;
        if (!$accept_temporary_remain) $accept_temporary_group="";
        if (!$anonymous) $anonymous="0";

        return true;
    }

    function RandomPassword($len=6) {
        $alf=array("1","2","3","4","5","6","7","8","9");
        $i=0;
        while($i < $len) {
            srand((double)microtime()*1000000);
            $randval = rand(0,8);
            $string="$string"."$alf[$randval]";
            $i++;
        }
        return $string;
    }

    function cleanURI($uri) {
        $uri=preg_replace("/.*sips?:([^;><=]+).*/", "\$1", $uri);
        return urlencode($uri);
    }
    
    function showUpgrade () {
    }

    function PhoneDialURL($uri) {
        $uri=$this->normalizeURI($uri);
        $uri_print="<a href=$uri>$this->call_img</a>";
        return $uri_print;
    }

    function showChapter($chapter) {
        print "
        <tr>
          <td height=3 colspan=2></td>
        </tr>
        <tr>
          <td class=border colspan=2 bgcolor=lightgrey><b>";
            print $chapter;
            print "</b>
          </td>
        </tr>
        ";
    }

    function normalizeURI($uri) {
        $uri=quoted_printable_decode($uri);

        $uri=preg_replace("/.*(sips?:[^;><=]+).*/", "\$1", $uri);
        if (preg_match("/^(sips?:.*):/", $uri, $m)) $uri=$m[1];

        if (preg_match("/^(.*sips?:0\d+)@(.*)(>?)$/",$uri,$m)) {
            $uri=$m[1]."@".$this->domain.$m[3];
        }
        return $uri;
    }

    function htmlURI($uri) {
        if (preg_match("/^sips?:00(\d+)@/",$uri,$m)) {
            $uri="+".$m[1];
        }
        return htmlentities($uri);
    }

    function colorizeDate($call_date) {
          list($date,$time)=explode(" ",$call_date);

        if ($date== Date("Y-m-d",time())) {
            $datePrint="<b><font color=green>".sprintf(_("Today"))."</font></b> ".$time;
        } else if ($date== Date("Y-m-d",time()-3600*24)) {
            $datePrint="<font color=blue>".sprintf(_("Yesterday"))."</font> ".$time;
        } else {
            $datePrint=$call_date;
        }
        return $datePrint;
    }

    function checkEmail($email) {
        dprint ("checkEmail($email)");
        $regexp = "/^([a-z0-9][a-z0-9_.-]*)@([a-z0-9][a-z0-9-]*\.)+([a-z]{2,})$/i";
        if (stristr($email,"-.") ||
            !preg_match($regexp, $email)) {
            return false;
        }
        return true;
    }

    function showPresence() {

        $this->getPresenceWatchers();
        $this->getPresenceRules();
        $this->getPresenceInformation();

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";
        $chapter=sprintf(_("Published information"));
        $this->showChapter($chapter);

        printf ("
        <tr>
        <td class=border>Note</td>
        <td class=border>Activity</td>
        </tr>");

        printf ("
        <tr>
          <td class=border><input type=text size=50 name=note value='%s'>
          <td class=border>
          <select name=activity>
        <option>
        ",$this->presentity['note']);

        $selected_activity[$this->presentity['activity']]='selected';

        foreach (array_keys($this->presenceActivities) as $_activity) {
            printf ("<option %s value='%s'>%s",$selected_activity[$_activity],$_activity,ucfirst($_activity));
        }
        print "</select>";

        if ($this->presenceActivities[$this->presentity['activity']]) {
            printf ("<img src=images/%s border=0>",$this->presenceActivities[$this->presentity['activity']]);
        }

        print "
          </td>
        </tr>
        ";

        $chapter=sprintf(_("Watchers"));
        $this->showChapter($chapter);

        $j=0;

        foreach (array_keys($this->presenceWatchers) as $_watcher) {
            $j++;

            $online_icon='';

            if (is_array($this->presenceRules['allow']) && in_array($_watcher,$this->presenceRules['allow'])) {
                $display_status = 'allow';
            } elseif (is_array($this->presenceRules['deny']) && in_array($_watcher,$this->presenceRules['deny'])) {
                $display_status = 'deny';
            } else {
                $display_status = $this->presenceWatchers[$_watcher]['status'];
            }

            if ($this->presenceWatchers[$_watcher]['online'] == 1) {
                $online_icon="<img src=images/buddy_online.jpg border=0>";
            } else {
                $online_icon="<img src=images/buddy_offline.jpg border=0>";
            }

            if ($display_status == 'deny') {
                $online_icon="<img src=images/buddy_banned.jpg border=0>";
                $color='red';
            } else if ($display_status == 'confirm') {
                $color='blue';
            } else {
                $color='green';
            }

            printf ("
            <tr>
            <input type=hidden name=watcher[] value=%s>
            <td class=border><font color=%s>%s</font></td>
            <td class=border>
            <select name=watcher_status[]>
            ",
            $_watcher,
            $color,
            $_watcher);

            unset($selected);
            $selected[$display_status]='selected';

            foreach ($this->presenceStatuses as $_status) {
                if ($_status== 'confirm' && !$selected[$_status]) continue;
                printf ("<option %s value=%s>%s",$selected[$_status],$_status,ucfirst($_status));
            }

            print "
            </select>
            $online_icon
            </td>
            </tr>
            ";
        }

        $chapter=sprintf(_("Rules"));
        $this->showChapter($chapter);

        $j=0;
        foreach (array_keys($this->presenceRules) as $_key) {
            $j++;

            foreach ($this->presenceRules[$_key] as $_tmp) {

                if (in_array($_tmp,array_keys($this->presenceWatchers))) {
                    continue;
                }

                printf ("
                <tr>
                <input type=hidden name=watcher[] value=%s>
                <td class=border>%s</td>
                <td class=border>
                <select name=watcher_status[]>
                ",$_tmp,$_tmp);

                unset($selected);
                $selected[$_key]='selected';

                foreach ($this->presenceStatuses as $_status) {
                    if ($_status== 'confirm' && !$selected[$_status]) continue;
                    printf ("<option %s value=%s>%s",$selected[$_status],$_status,ucfirst($_status));
                }
                print "
                <option value=delete>Delete
                </select>
                ";

                if ($_key == 'deny') {
                    print "<img src=images/buddy_banned.jpg border=0>";
                }
                print "
                </td>
                </tr>
                ";
            }
        }

        printf ("
        <tr>
        <td class=border><input type=text name=watcher[]></td>
        <td class=border>
        <select name=watcher_status[]>
        ");
        $selected['deny']='selected';
        foreach ($this->presenceStatuses as $_status) {
            printf ("<option %s value=%s>%s",$selected[$_status],$_status,ucfirst($_status));
        }

        print "
        </select>
        </td>
        </tr>
        ";

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set presence\">
        ";

        print "
        <input type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";
        print "
          </td>
          <td align=right>
          </td>
        </tr>
        ";

        print $this->hiddenElements;

        print "
        </form>
        ";

    }

    function setPresence() {

        // publish information
        $this->setPresenceInformation();

        // set policy
        unset($policy);
        foreach ($this->presenceStatuses as $_status) {
            $policy[$_status]=array();
        }

        $j=0;
        $seen_watcher=array();
        foreach($_REQUEST['watcher'] as $_w) {
            if (!strlen($_w)) continue;
            if ($seen_watcher[$_w]) continue;
            $seen_watcher[$_w]++;
            if (!strstr($_w,'@')) {
                if ($_REQUEST['watcher_status'][$j] == 'delete') continue;
                $domain_policy[$_REQUEST['watcher_status'][$j]][]=$_w;
            }
            $j++;
        }

        $j=0;
        $seen_watcher=array();
        foreach($_REQUEST['watcher'] as $_w) {
            if (!strlen($_w)) continue;
            if ($seen_watcher[$_w]) continue;
            $seen_watcher[$_w]++;

             if ($_REQUEST['watcher_status'][$j] == 'delete') continue;

            $status=$_REQUEST['watcher_status'][$j];
            list($u,$d)=explode('@',$_w);
            if (!in_array($d,$domain_policy[$status])) {
                $policy[$status][]=$_w;
            }
            $j++;
        }

        //dprint_r($policy);

        $result = $this->PresencePort->setPolicy(array("username" =>$this->username,"domain"   =>$this->domain),$this->password,$policy);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (PresencePort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

    }

    function getPresenceWatchers () {
        dprint("getPresenceWatchers()");
        $result = $this->PresencePort->getWatchers(array("username" =>$this->username,"domain"   =>$this->domain),$this->password);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (PresencePort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        dprint_r($result);

        foreach ($result as $_watcher) {
            $this->presenceWatchers[$_watcher->id]['status']=$_watcher->status;
            $this->presenceWatchers[$_watcher->id]['online']=$_watcher->online;
            $this->watchersOnline=0;
            if ($this->presenceWatchers[$_watcher->id]['online']) {
                $this->watchersOnline++;
            }
        }
        //dprint_r($this->presenceWatchers);

    }

    function getPresenceInformation() {
        dprint("getPresenceInformation()");
        $result = $this->PresencePort->getPresenceInformation(array("username" =>$this->username,"domain"   =>$this->domain),$this->password);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (PresencePort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        dprint_r($result);

        $this->presentity['activity'] = $result->activity;
        $this->presentity['note']     = $result->note;
    }

    function setPresenceInformation() {

        $presentity['activity'] = $_REQUEST['activity'];
        $presentity['note']     = $_REQUEST['note'];

        if (strlen($presentity['activity']) && strlen($presentity['note'])) {
            $result = $this->PresencePort->setPresenceInformation(array("username" =>$this->username,"domain"   =>$this->domain),$this->password, $presentity);
        } else if (!strlen($presentity['note'])) {
            $result = $this->PresencePort->deletePresenceInformation(array("username" =>$this->username,"domain"   =>$this->domain),$this->password);
        } else {
            return true;
        }

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (PresencePort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        return true;
    }

    function getPresenceRules () {
        dprint("getPresenceRules()");
        $result = $this->PresencePort->getPolicy(array("username" =>$this->username,"domain"   =>$this->domain),$this->password);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (PresencePort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        foreach ($this->presenceStatuses as $_status) {
            $this->presenceRules[$_status] = $result->$_status;
        }

        //dprint_r($this->presenceRules);
    }

    function getFileTemplate($name, $type="file") {
    
        //dprint("getFileTemplate(name=$name, type=$type, path=$this->templates_path)");

        if ($type=='logo') {
            $extensions=array('png','gif','jpg');
            foreach ($extensions as $_ext) {

                $file=$this->templates_path.'/'.$this->reseller.'/'.$name.'.'.$_ext;
                if (file_exists($file)) {
                    return $file;
                }
            }
            foreach ($extensions as $_ext) {
                if (file_exists("$this->templates_path/default/$name.$_ext")) {
                    return "$this->templates_path/default/$name.$_ext";
                }
            }
            return false;

        } else {
            if (file_exists("$this->templates_path/$this->reseller/$name")) {
                return "$this->templates_path/$this->reseller/$name";
            } elseif (file_exists("$this->templates_path/default/$name")) {
                return "$this->templates_path/default/$name";
            } else {
                return false;
            }
        }
    }
    
    function getEmailTemplate($language='en') {
        $file = "sip_settings_email_$language.tpl";
        $file2 = "sip_settings_email.tpl";

        //print("templates_path = $this->templates_path");

        if (file_exists("$this->templates_path/$this->reseller/$file")) {
            return "$this->templates_path/$this->reseller/$file";
        } elseif (file_exists("$this->templates_path/$this->reseller/$file2")) {
            return "$this->templates_path/$this->reseller/$file2";
        } elseif (file_exists("$this->templates_path/default/$file")) {
            return "$this->templates_path/default/$file";
        } elseif (file_exists("$this->templates_path/default/$file2")) {
            return "$this->templates_path/default/$file2";
        } else {
            return false;
        }
    }

    function getBillingProfiles() {
        // Get getBillingProfiles
        if ($this->SOAPversion < 2) return true;
        dprint("getBillingProfiles()");

        $this->RatingPort->addHeader($this->SoapAuth);
        $result     = $this->RatingPort->getEntityProfiles("subscriber://".$this->account);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "4001") {
                printf ("<p><font color=red>Error (Rating): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        }

        $this->billingProfiles=$result;
    }

    function showBillingProfiles() {
        if ($this->login_type != 'reseller' && $this->login_type != 'admin') {
            return false;
        }

        if (!$this->pstn_changes_allowed) {
            return false;
        }

        $this->getBillingProfiles();

        $chapter=sprintf(_("Billing profiles"));
        $this->showChapter($chapter);

        print "
        <tr>
          <td class=border valign=top>";
            print _("Profiles");
            printf ("
          </td>
          <td class=border align=left>
            Weekday:
            <input type=text size=10 maxsize=64 name=profileWeekday value='%s'>/
            <input type=text size=10 maxsize=64 name=profileWeekdayAlt value='%s'>
            Weekend:
            <input type=text size=10 maxsize=64 name=profileWeekend value='%s'>/
            <input type=text size=10 maxsize=64 name=profileWeekendAlt value='%s'>
            ",
            $this->billingProfiles->profileWeekday,
            $this->billingProfiles->profileWeekdayAlt,
            $this->billingProfiles->profileWeekend,
            $this->billingProfiles->profileWeekendAlt
            );

            if ($this->billingProfiles->timezone) {
                $_timezone=$this->billingProfiles->timezone;
            } else {
                $_timezone=$this->resellerProperties['timezone'];
            }

            print "<br>Timezone: ";
            $this->showTimezones('profileTimezone',$_timezone);

            print "
          </td>
        </tr>
        ";
    }

    function updateBillingProfiles() {
        if ($this->login_type != 'reseller' && $this->login_type != 'admin') {
            return false;
        }

        if (!$this->pstn_changes_allowed) {
            return true;
        }

        $this->RatingPort->addHeader($this->SoapAuth);
        $result     = $this->RatingPort->getEntityProfiles("subscriber://".$this->account);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "4001") {
                printf ("<p><font color=red>Error (Rating): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        }

        $this->billingProfiles=$result;

        $profiles=array("entity"            =>'subscriber://'.$this->account ,
                        "profileWeekday"    => trim($_REQUEST['profileWeekday']),
                        "profileWeekdayAlt" => trim($_REQUEST['profileWeekdayAlt']),
                        "profileWeekend"    => trim($_REQUEST['profileWeekend']),
                        "profileWeekendAlt" => trim($_REQUEST['profileWeekendAlt']),
                        "timezone"          => trim($_REQUEST['profileTimezone'])
                        );

        //print_r($profiles);

        $this->RatingPort->addHeader($this->SoapAuth);
        if ($this->billingProfiles->profileWeekday && !$profiles['profileWeekday']) {
            // delete profile
            $result     = $this->RatingPort->deleteEntityProfiles('subscriber://'.$this->account);
            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                if ($error_fault->detail->exception->errorcode != "4001") {
                    printf ("<p><font color=red>Error (Rating): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    return false;
                }
            }

        } else if ($profiles['profileWeekday']) {
            // update profile

            $result     = $this->RatingPort->setEntityProfiles($profiles);

            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                if ($error_fault->detail->exception->errorcode != "4001") {
                    printf ("<p><font color=red>Error (Rating): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    return false;
                }
            }
        }
    }

    function getImageForUserAgent($agent) {

        // array with mappings between User Agents and images
        foreach ($this->userAgentImages as $agentRegexp => $image) {
            if (preg_match("/$agentRegexp/i", $agent)) {
                return $image;
            }
        }
    
        return "unknown.png";
    }

    function showExtraGroups () {
		if ($this->disable_extra_groups) return true;

        $foundGroupInAvailableGroups=array();

        foreach ($this->groups as $_grp) {
        	foreach (array_keys($this->availableGroups) as $a_grp) {
                if ($_grp == $a_grp) $foundGroupInAvailableGroups[]=$_grp;
                continue;
            }
        }

        $extraGroups = array_unique(array_diff($this->groups,$foundGroupInAvailableGroups));

        foreach ($extraGroups as $_eg) {
        	$extraGroups_text.=$_eg.' ';
        }

        print "
        <tr>
        <td class=border>";
        print _("Extra groups");
        print "
        </td>
        <td class=ag1>";
        printf ("<input type=text size=30 name=extra_groups value='%s'>",trim($extraGroups_text));
        print "
        </td>
        </tr>
        ";
    }

    function generateCertificate() {

		global $enrollment;
		require_once("/etc/cdrtool/enrollment/config.ini");
        $this->enrollment=$enrollment;

    	$config = array(
    		'config'           => $this->enrollment['ca_conf'],
    		'digest_alg'       => 'md5',
    		'private_key_bits' => 1024,
    		'private_key_type' => OPENSSL_KEYTYPE_RSA,
    		'encrypt_key'      => false,
    	);

		$dn = array(
    		"countryName"            => "NL",
	    	"stateOrProvinceName"    => "NH",
    		"localityName"           => "Amsterdam",
    		"organizationName"       => "AG Projects",
    		"organizationalUnitName" => "SIP certificate",
    		"commonName"             => $this->account,
    		"emailAddress"           => $this->email
		);

		$this->key = openssl_pkey_new($config);
        if ($this->key==FALSE) {
			while (($e = openssl_error_string()) !== false) {
				echo $e . "\n";
				print "<br><br>";
			}
            return false;
		}

		$this->csr = openssl_csr_new($dn, $this->key);

        if (!$this->csr) {
			while (($e = openssl_error_string()) !== false) {
				echo $e . "\n";
				print "<br><br>";
			}
            return false;
		}

		$ca="file://".$this->enrollment['ca_crt'];

		$this->crt = openssl_csr_sign($this->csr, $ca, $this->enrollment['ca_key'], 3650, $config);

		if ($this->crt==FALSE) {
			while (($e = openssl_error_string()) !== false) {
				echo $e . "\n";
				print "<br><br>";
			}
            return false;
		}

        openssl_csr_export    ($this->csr, $this->csr_out);
        openssl_pkey_export   ($this->key, $this->key_out, $this->password, $config);
        openssl_x509_export   ($this->crt, $this->crt_out);
        openssl_pkcs12_export ($this->crt, $this->p12_out, $this->key, $this->password);

        $ret=array(  'csr' => $this->csr_out,
                     'crt' => $this->crt_out,
                     'key' => $this->key_out,
                     'pkey'=> $public_key,
                     'p12' => $this->p12_out,
                     'ca'  => file_get_contents($this->enrollment['ca_crt'])
                     );
        return $ret;

    }

    function exportCertificateKey() {
        $cert=$this->generateCertificate();
        Header("Content-type: application/x-key");
		Header("Content-Disposition: inline; filename=blink.pkey");
        print $cert['key'];
    }

    function exportCertificateCsr() {
        Header("Content-type: application/x-csr");
		Header("Content-Disposition: inline; filename=blink.csr");
        $cert=$this->generateCertificate();
        print $cert['csr'];
    }

    function exportCertificateCrt() {
        Header("Content-type: application/x-crt");
		Header("Content-Disposition: inline; filename=blink.crt");
        $cert=$this->generateCertificate();
        print $cert['crt'];
    }

    function exportCertificateP12() {
        $cert=$this->generateCertificate();
        Header("Content-type: application/x-p12");
		Header("Content-Disposition: inline; filename=blink.p12");
        print $cert['p12'];
    }

}

function normalizeURI($uri) {
    $uri=quoted_printable_decode($uri);
    if (preg_match("/^(.*<sips?:.*)@(.*>)/",$uri,$m)) {
        if (preg_match("/^(.*):/U",$m[2],$p)){
            $uri=$m[1]."@".$p[1].">";
        } else {
            $uri=$m[1]."@".$m[2];
        }
    } else if (preg_match("/^(sips?:.*)[=:;]/U",$uri,$p)) {
        $uri=$p[1];
    }

    return $uri;
}

function normalizeTime($period) {
    $sec=$period%60;
    $min=floor($period/60);
    $h=floor($min/60);

    if (!$period) return ;

    if ($h>0) {
        $min=$min-60*$h;
    }
    if ($h >= 1) {
        return sprintf('%dh%02d\'%02d"', $h, $min, $sec);
    } else {
        return sprintf('%d\'%02d"', $min, $sec);
    }
}

function checkSIPURI($uri) {
    if ($uri == "<voice-mailbox>") return true;

    $regexp = "/^sips?:([a-z0-9][a-z0-9_.-]*)@([a-z0-9][a-z0-9-]*\.)+([a-z0-9]{2,})$/i";
    if (stristr($contact,"-.") || !preg_match($regexp, $uri)) {
        return false;
    }
    return true;
}

function checkURI($uri) {
    //dprint ("<b>checkURI($uri) </b>");
    if ($uri == "<voice-mailbox>") return true;

    if (preg_match("/^(sip:|sips:)(.*)$/",$uri,$m)) $uri=$m[2];

    $regexp = "/^(\+?[a-z0-9*][a-z0-9_.*-]*)@([a-z0-9][a-z0-9-]*\.)+(([a-z]{2,})|(\d+))$/i";
    if (stristr($uri,"-.") ||
        !preg_match($regexp, $uri)) {
        print "Invalid URI \"$uri\". ";
        return false;
    }
    return true;
}

function checkPhonebookURI($uri) {
    $regexp = "/^sip:([a-z0-9%_.-]*)@([a-z0-9%.-]*)$/i";
    if (stristr($contact,"-.") || !preg_match($regexp, $uri)) {
        print "Invalid URI \"$uri\". ";
        return false;
    }
    return true;
}

function getLocalTime($timezone,$timestamp) {
    $tz=getenv('TZ');
    putenv("TZ=$timezone");
    $LocalTime=date("Y-m-d H:i:s", $timestamp);
    putenv("TZ=$tz");
    return $LocalTime;
}

function getSipThorHomeNode ($account,$sip_proxy) {
    if (!$account || !$sip_proxy) return false;
    $socket = fsockopen($sip_proxy, 9500, $errno, $errstr, 1);
    if (!$socket) {
        return false;
    }
    
    $request=sprintf("lookup sip_proxy for %s",$account);
    
    if (fputs($socket,"$request\r\n") !== false) {
        $ret = fgets($socket,4096);
    }
    fclose($socket);
    return $ret;
}

?>
