<?
/*
    Copyright (c) 2007-2010 AG Projects
    http://ag-projects.com
    Author Adrian Georgescu
    
    This library provide the functions for managing properties
    of SIP Accounts retrieved from NGNPro

*/

require_once("ngnpro_client.php");

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
    var $billing_email             = "Billing <billing@example.com>";
    var $sip_settings_page         = "https://cdrtool.example.com/sip_settings.phtml";
    var $blink_settings_page       = "https://cdrtool.example.com/sip_settings_digest.phtml";
    var $xcap_root                 = "https://cdrtool.example.com/xcap-root";
    var $pstn_access               = false;
    var $sms_access                = false;
    var $pstn_changes_allowed      = false;
    var $prepaid_changes_allowed   = false;
    var $sip_proxy                 = "proxy.example.com";
    var $voicemail_server          = "vm.example.com";
    var $absolute_voicemail_uri    = false; // use <voice-mailbox>
    var $enable_thor               = false;
    var $currency                  = "&euro";
    // access numbers

    var $voicemail_access_number        = "1233";
    var $FUNC_access_number             = "*21*";
    var $FNOL_access_number             = "*22*";
    var $FNOA_access_number             = "*23*";
    var $FBUS_access_number             = "*23*";
    var $change_privacy_access_number   = "*67";
    var $check_privacy_access_number    = "*68";
    var $reject_anonymous_access_number = "*69";

	var $show_barring_tab   = true;
	var $show_presence_tab  = false;
    var $show_payments_tab  = false;
    var $show_download_tab  = true;
    var $show_support_tab   = false;
    var $show_directory     = false;

    var $first_tab          = 'calls';
    var $auto_refesh_tab    = 0;              // number of seconds after which to refresh tab content in the web browser

	var $payment_processor_class = false;

    // end variables

    var $tab                       = "settings";
    var $phonebook_img             = "<img src=images/pb.gif border=0>";
    var $call_img                  = "<img src=images/call.gif border=0 alt='Dial'>";
    var $delete_img                = "<img src=images/del_pb.gif border=0 alt='Delete'>";
    var $plus_sign_img             = "<img src=images/plus_sign.png border=0 alt='Add Contact'>";
    var $embedded_img              = "<img src=images/blink.png border=0>";

    var $groups                    = array();

    var $form_elements             = array(
                                           'mailto',
                                           'free-pstn',
                                           'blocked',
                                           'sip_password',
                                           'first_name',
                                           'last_name',
                                           'quota',
                                           'language',
                                           'quota_deblock',
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
                                           'extra_groups',
                                           'show_presence_tab',
                                           'show_barring_tab'
                                           );

    var $presence_statuses   = array('allow','deny','confirm');
    var $presence_activities = array('busy'      => 'buddy_busy.jpg',
                                    'open'      => 'buddy_online.jpg',
                                    'available' => 'buddy_online.jpg',
                                    'idle'      => 'buddy_idle.jpg',
                                    'away'      => 'buddy_away.jpg'
                                    );

	var $disable_extra_groups=true;

    var $prepaid             = 0;
    var $emergency_regions   = array();
    var $FNOA_timeoutDefault = 35;
    var $presence_rules      = array();
    var $enums               = array();
    var $barring_prefixes    = array();
    var $presence_watchers   = array();
    var $SipUAImagesPath     = "images";
    var $SipUAImagesFile     = "phone_images.php";
    var $balance_history     = array();
    var $enrollment_url      = false;
    var $sip_settings_api_url= false;

	var $owner_information   =array();

    var $languages=array("en"=>array('name'=>"English",
                                     'timezone'=>''
                                     ),
                         "ro"=>array('name'=>"Română",
                                     'timezone' => 'Europe/Bucharest'
                                     ),
                         "nl"=>array('name'=>"Nederlands",
                                     'timezone' => 'Europe/Amsterdam'
                                     ),
                         "es"=>array('name'=>"Español",
                                     'timezone' => 'Europe/Madrid'
                                     ),
                         "de"=>array('name'=>"Deutsch",
                                     'timezone' => 'Europe/Berlin'
                                     )
                         );

	var $pstn_termination_price_page = 'sip_rates.html';
    var $append_domain_to_xcap_root = false;
    var $blink_download_url   = "https://blink.sipthor.net/download.phtml?download";
    var $ownerCredentials = array();

    function SipSettings($account,$loginCredentials=array(),$soapEngines=array()) {

		define_syslog_variables();

        $this->soapEngines        = $soapEngines;

        $debug=sprintf("<font color=blue><p><b>Initialize %s(%s)</b></font>",get_class($this),$account);
        dprint($debug);
        //dprint_r($loginCredentials);

        $this->loginCredentials = &$loginCredentials;

		if ($this->isEmbedded()) {
        	$this->login_type = 'subscriber';
        } else {
            if ($loginCredentials['login_type']) {
                $this->login_type = $loginCredentials['login_type'];
            } else {
                $this->login_type = 'subscriber';
            }
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
        } else {
            print _("Error: missing sip_engine in login credentials");
            return false;
        }

        $this->settingsPage       = $_SERVER['PHP_SELF'];

        if ($_REQUEST['tab']) {
        	$this->tab                = $_REQUEST['tab'];
        } else {
        	$this->tab                = $this->first_tab;
        }

        $this->initSoapClient();

        $this->getAccount($account);

        if ($this->tab=='calls' && !$_REQUEST['export']) {
        	$this->auto_refesh_tab=180;
        }

        $this->admin_url      = $this->settingsPage."?account=$this->account&adminonly=1&reseller=$this->reseller&sip_engine=$this->sip_engine";
        $this->reseller_url   = $this->settingsPage."?account=$this->account&reseller=$this->reseller&sip_engine=$this->sip_engine";

        $this->admin_url_absolute = $this->sip_settings_page."?account=$this->account&adminonly=1&reseller=$this->reseller&sip_engine=$this->sip_engine";

        if ($this->login_type == "admin") {
            $this->url=$this->admin_url;
            $this->hiddenElements="
            <input type=hidden name=account value=\"$this->account\">
            <input type=hidden name=reseller value=$this->reseller>
            <input type=hidden name=sip_engine value=$this->sip_engine>
            <input type=hidden name=adminonly value=1>
            ";

        } else if ($this->login_type == "reseller") {

            $this->url=$this->reseller_url;
            $this->hiddenElements="
            <input type=hidden name=account value=\"$this->account\">
            <input type=hidden name=reseller value=$this->reseller>
            <input type=hidden name=sip_engine value=$this->sip_engine>
            ";

        } else if ($this->login_type == "customer") {

            $this->url=$this->reseller_url;
            $this->hiddenElements="
            <input type=hidden name=account value=\"$this->account\">
            <input type=hidden name=reseller value=$this->reseller>
            <input type=hidden name=sip_engine value=$this->sip_engine>
            ";

        } else {
        	$this->url=$this->settingsPage;
			if (!$this->isEmbedded()) {
            	$this->url.="?account=$this->account";
            } else {
            	$this->url.=sprintf("?1=1&realm=%s",urlencode($_REQUEST['realm']));
                if ($_REQUEST['user_agent']) {
            		$this->url.=sprintf("&user_agent=%s",urlencode($_REQUEST['user_agent']));
                }
            }

            $this->hiddenElements=sprintf("
            <input type=hidden name=account value='%s'>
            <input type=hidden name=sip_engine value='%s'>
            <input type=hidden name=user_agent value='%s'>
            <input type=hidden name=realm value='%s'>
            ",
            $this->account,
            $this->sip_engine,
            $_REQUEST['user_agent'],
            $_REQUEST['realm']
            );
        }

        $this->setLanguage();

        if (!$this->username) {
            dprint ("Record not found.");
            return false;
        }

        $this->availableGroups['blocked']  = array("Group"=>"blocked",
                                        "WEBName" =>sprintf(_("Status")),
                                        "SubscriberMayEditIt"=>0,
                                        "SubscriberMaySeeIt"=>0,
                                        "ResellerMayEditIt"=>1,
                                        "ResellerMaySeeIt"=>1
                                        );

        $this->availableGroups['payments'] = array("Group"=>"payments",
                                        "WEBName" =>sprintf(_("CC Payments")),
                                        "SubscriberMayEditIt"=>0,
                                        "SubscriberMaySeeIt"=>0,
                                        "ResellerMayEditIt"=>1,
                                        "ResellerMaySeeIt"=>1
                                        );

        $this->getResellerSettings();
        $this->getCustomerSettings();

        if ($this->change_privacy_access_number) {
            $_comment=sprintf(_("Dial %s to change"),$this->change_privacy_access_number);
        } else {
            $_comment='';
        }
        $this->availableGroups['anonymous']=array("Group"=>"anonymous",
                                    "WEBName" =>sprintf (_("PSTN Privacy")),
                                    "WEBComment"=>$_comment,
                                    "SubscriberMaySeeIt"=>1,
                                    "SubscriberMayEditIt"=>1,
                                    "ResellerMayEditIt"=>1,
                                    "ResellerMaySeeIt"=>1
                                    );
        if ($this->change_privacy_access_number) {
            $_comment=sprintf(_("Dial %s to change"),$this->reject_anonymous_access_number);
        } else {
            $_comment='';
        }
 
        $this->availableGroups['anonymous-reject']=array("Group"=>$this->anonymous-reject,
                                    "WEBName" =>sprintf (_("Reject Anonymous")),
                                    "WEBComment"=>$_comment,
                                    "SubscriberMaySeeIt"=>1,
                                    "SubscriberMayEditIt"=>1,
                                    "ResellerMayEditIt"=>1,
                                    "ResellerMaySeeIt"=>1
                                                        );
        $this->availableGroups['missed-calls']=array("Group"=>'missed-calls',
                                    "WEBName" =>sprintf (_("Email Missed Calls")),
                                    "WEBComment"=>'',
                                    "SubscriberMaySeeIt"=>1,
                                    "SubscriberMayEditIt"=>1,
                                    "ResellerMayEditIt"=>1,
                                    "ResellerMaySeeIt"=>1

                                    );

        $this->pstnChangesAllowed();
        $this->smsChangesAllowed();
        $this->prepaidChangesAllowed();

        $this->tabs=array('identity'=>_('Identity'),
                          'devices'=>_('Devices'),
                          'settings'=>_('Settings'),
                          'diversions'=>_('Forwarding'),
                          'accept' =>_("DND"),
                          'contacts'=>_("Contacts"),
                          'calls'=>_('History'),
                          );


        if (in_array("free-pstn",$this->groups)) {
            if ($this->Preferences['show_barring_tab']) {
            	$this->tabs['barring']=_("Barring");
            }
        }

        if ($this->presence_engine && $this->show_presence_tab) {
            if ($this->Preferences['show_presence_tab']) {
            	$this->tabs['presence']=_("Presence");
            }
        }

		if (!$this->isEmbedded() && $this->show_download_tab) {
        	$this->tabs['download'] = 'Blink';
        }

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

        $this->diversionType=array(
        "FUNC"=>sprintf(_("All Calls")),
        "FNOL"=>sprintf(_("If Not-Online")),
        "FBUS"=>sprintf(_("If Busy")),
        "FNOA"=>sprintf(_("If No-Answer")),
        "FUNV"=>sprintf(_("If Unavailable"))
        );

        $this->diversionTypeUNV=array(
        "FUNV"=>sprintf(_("If Unavailable"))
        );

        $this->VoicemaildiversionType=array(
        "FNOL"=>sprintf(_("If Not-Online")),
        "FBUS"=>sprintf(_("If Busy")),
        "FNOA"=>sprintf(_("If No-Answer")),
        "FUNV"=>sprintf(_("If Unavailable"))
        );

        $this->access_numbers=array("FUNC"=>$this->FUNC_access_number,
                                    "FNOA"=>$this->FNOA_access_number,
                                    "FBUS"=>$this->FBUS_access_number,
                                    "FNOL"=>$this->FNOL_access_number
                                    );
        if ($this->prepaid) {
            $this->tabs['credit']=_("Credit");
        }

		$_protocol=preg_match("/^(https?:\/\/)/",$_SERVER['SCRIPT_URI'],$m);
        $this->absolute_url=$m[1].$_SERVER['HTTP_HOST'].$this->url;
        //dprint($this->absolute_url);

        if ($this->prepaid && $this->show_payments_tab) {
        	$this->tabs['payments']=_("Payments");
        }

        if ($this->show_support_tab) {
        	$this->tabs['support'] = 'Support';
        }

    }

    function initSoapClient() {
        dprint("initSoapClient()");

        // Sip, Voicemail and Customer ports share same login
        $this->SOAPurl=$this->soapEngines[$this->sip_engine]['url'];

        $this->SOAPversion=$this->soapEngines[$this->sip_engine]['version'];

        if ($this->soapEngines[$this->sip_engine]['enrollment_url']) {
        	$this->enrollment_url =$this->soapEngines[$this->sip_engine]['enrollment_url'];
        }

        if ($this->soapEngines[$this->sip_engine]['sip_settings_api_url']) {
        	$this->sip_settings_api_url =$this->soapEngines[$this->sip_engine]['sip_settings_api_url'];
        }

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

        $this->SOAPloginAdmin = array(
                               "username"    => $this->soapEngines[$this->sip_engine]['username'],
                               "password"    => $this->soapEngines[$this->sip_engine]['password'],
                               "admin"       => true
                               );

        $this->SoapAuthAdmin = array('auth', $this->SOAPloginAdmin , 'urn:AGProjects:NGNPro', 0, '');

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

        if (strlen($this->soapEngines[$this->sip_engine]['billing_email'])) {
            $this->billing_email    = $this->soapEngines[$this->sip_engine]['billing_email'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['sip_settings_page'])) {
            $this->sip_settings_page = $this->soapEngines[$this->sip_engine]['sip_settings_page'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['blink_settings_page'])) {
            $this->blink_settings_page = $this->soapEngines[$this->sip_engine]['blink_settings_page'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['xcap_root'])) {
            $this->xcap_root       = $this->soapEngines[$this->sip_engine]['xcap_root'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['cdrtool_address'])) {
            $this->cdrtool_address  = $this->soapEngines[$this->sip_engine]['cdrtool_address'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['msrp_relay'])) {
            $this->msrp_relay  = $this->soapEngines[$this->sip_engine]['msrp_relay'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['emergency_regions'])) {
            $this->emergency_regions  = $this->soapEngines[$this->sip_engine]['emergency_regions'];
        }

        if ($this->soapEngines[$this->sip_engine]['pstn_access']) {
            $this->pstn_access     = $this->soapEngines[$this->sip_engine]['pstn_access'];
        }

        if ($this->soapEngines[$this->sip_engine]['sms_access']) {
            $this->sms_access     = $this->soapEngines[$this->sip_engine]['sms_access'];
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

        if ($this->soapEngines[$this->sip_engine]['FNOL_access_number']) {
            $this->FNOL_access_number = $this->soapEngines[$this->sip_engine]['FNOL_access_number'];
        }

        if ($this->soapEngines[$this->sip_engine]['payment_processor_class']) {
            $this->payment_processor_class = $this->soapEngines[$this->sip_engine]['payment_processor_class'];
        }

        if ($this->soapEngines[$this->sip_engine]['change_privacy_access_number']) {
            $this->change_privacy_access_number = $this->soapEngines[$this->sip_engine]['change_privacy_access_number'];
        }

        if ($this->soapEngines[$this->sip_engine]['check_privacy_access_number']) {
            $this->check_privacy_access_number = $this->soapEngines[$this->sip_engine]['check_privacy_access_number'];
        }

        if ($this->soapEngines[$this->sip_engine]['reject_anonymous_access_number']) {
            $this->reject_anonymous_access_number = $this->soapEngines[$this->sip_engine]['reject_anonymous_access_number'];
        }

        if ($this->soapEngines[$this->sip_engine]['show_presence_tab']) {
            $this->show_presence_tab = $this->soapEngines[$this->sip_engine]['show_presence_tab'];
        }

        if ($this->soapEngines[$this->sip_engine]['show_directory']) {
            $this->show_directory = $this->soapEngines[$this->sip_engine]['show_directory'];
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

        if (!$result->quota) $result->quota=0;

        foreach ($result->properties as $_property) {
            $this->Preferences[$_property->name]=$_property->value;
        }

        //dprint_r($this->Preferences);

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
        $this->name      = $this->firstName; // used by smarty

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

        if ($this->append_domain_to_xcap_root) {
        	$this->xcap_root     = rtrim($this->xcap_root,'/')."@".$this->domain."/";
        }

        $this->result    = $result;

    }

    function showAccount() {
        dprint('showAccount()');

        if (!$this->account) {
            print "<tr><td>";
            print _("Error: SIP Account information cannot be retrieved. ");
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


        // show tab
        $tabFunctionName='show'.ucfirst($this->tab).'Tab';

        $this->$tabFunctionName();

        $this->showFooter();

    	$this->chapterTableStop();
    }

    function getDomainOwner ($domain='') {
        dprint("getdomainOwner($domain)");

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
            printf ("<p><font color=red>Error %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            if ($result->domains[0]) {
                $this->reseller  = $result->domains[0]->reseller;
                $this->customer  = $result->domains[0]->customer;
            }
        }
    }

    function getMobileNumber() {
        //dprint('getMobileNumber()');
        $this->mobile_number='';

        if ($this->Preferences['mobile_number']) {
            $this->mobile_number=$this->Preferences['mobile_number'];
        } else if ($this->owner_information['mobile']) {
            $this->mobile_number=$this->owner_information['mobile'];
        }
    }

    function setLanguage() {
        dprint("setLanguage()");

        if ($this->login_type == 'reseller' || $this->login_type == 'customer') {
            $lang = $this->ResellerLanguage;
        } else if ($this->login_type == 'subscriber') {
            if (!$this->Preferences['language']) {
                foreach (array_keys($this->languages) as $_lang) {
                    if ($this->languages[$_lang]['timezone'] == $this->timezone) {
                        $lang=$_lang;
                        break;
                    }
                }
            }
            $lang = $this->Preferences['language'];
        } else {
            $lang = "en";
        }

        //print("Set language to $lang");
        $this->changeLanguage($lang);
    }

    function getOwnerSettings($owner='') {
        dprint("getOwnerSettings($owner)");
        if (!$owner) {
            return false;
        }

        $this->CustomerPort->addHeader($this->SoapAuthCustomer);
        $result     = $this->CustomerPort->getAccount($owner);
 
        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (CustomerPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }
 
        $this->owner_information=array(
                               "username"            => $result->username,
                               "password"            => $result->password,
                               "firstName"           => $result->firstName,
                               "lastName"            => $result->lastName,
                               "organization"        => $result->organization,
                               "timezone"            => $result->timezone,
                               "address"             => $result->address,
                               "billingAddress"      => $result->billingAddress,
                               "city"                => $result->city,
                               "state"               => $result->state,
                               "country"             => $result->country,
                               "postcode"            => $result->postcode,
                               "tel"                 => $result->tel,
                               "enum"                => $result->enum,
                               "mobile"              => $result->mobile,
                               "fax"                 => $result->fax,
                               "email"               => $result->email,
                               "web"                 => $result->web
                               );
        //dprint_r($this->owner_information);

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
        dprint("getVoicemail()");

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
        <td colspan=2 bgcolor=lightgrey>
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
    
        if ($this->login_type == 'subscriber' && !$this->isEmbedded()) {
            print "<a href=sip_logout.phtml>";
            print _("Logout");
            print "</a>";
        } else {
            if ($this->enable_thor) {
                print " ";
                if ($this->isEmbedded()) {
                    print _("Home Node");
                } else {
                    print "<a href=\"http://www.ag-projects.com/SIPThor.html\" target=_new>";
                    print _("SIP Thor Node");
                    print "</a>";
                }

                if ($this->homeNode=getSipThorHomeNode($this->account,$this->sip_proxy)) {
                    printf (" <font color=green>%s</font>",$this->homeNode);
                } else {
                    print "<font color=red>";
                    print _("Unknown");
                    print "</font>";
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

                if ($this->owner_information['tel']) {
                    $tel  = preg_replace("/[^\d+]/", "", $this->owner_information['tel']);
                    $tel_enum = str_replace("+", "00", $tel);
                    $telf = $tel_enum . "@" . $this->domain;
                    if (!$seen[$tel_enum] && !in_array($tel_enum,$this->enums)) {
                        $this->divertTargets[]=array("name"  => sprintf (_("Tel %s"),$tel),
                                                      "value" => $telf,
                                                      "description"  => "Tel");
                    }
                    $seen[$tel_enum]++;
                }

                if ($this->owner_information['enum']) {
                    $tel  = preg_replace("/[^\d+]/", "", $this->owner_information['enum']);
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
                if ($this->resellerProperties['pstn_access']) {
                	dprint("is reseller");
                	$this->pstn_changes_allowed = true;
                }
                return;
            } else if ($this->customerImpersonate == $this->loginCredentials['reseller']) {
                if ($this->resellerProperties['pstn_access']) {
                	dprint("impersonate reseller");
                	$this->pstn_changes_allowed = true;
                }
                return;
            }
        } else if ($this->login_type == 'customer') {
            if ($this->resellerProperties['pstn_access'] && $this->customerProperties['pstn_access']) {
                $this->pstn_changes_allowed = true;
                return;
            }
        }

        $this->pstn_changes_allowed = false;
        return;
    }

    function smsChangesAllowed() {
        dprint("smsChangesAllowed()");
        if ($this->login_type == 'subscriber') {
            $this->sms_changes_allowed = false;
            return;
        } else if ($this->login_type == 'admin') {
            $this->sms_changes_allowed = true;
            return;
        } else if ($this->login_type == 'reseller') {

            // for a reseller we need to check if a subaccount is allowed
            if ($this->loginCredentials['customer'] == $this->loginCredentials['reseller']) {
                if ($this->resellerProperties['sms_access']) {
                	dprint("is reseller");
                	$this->sms_changes_allowed = true;
                }
                return;
            } else if ($this->customerImpersonate == $this->loginCredentials['reseller']) {
                if ($this->resellerProperties['sms_access']) {
                	dprint("impersonate reseller");
                	$this->sms_changes_allowed = true;
                }
                return;
            }
        } else if ($this->login_type == 'customer') {
            if ($this->resellerProperties['sms_access'] && $this->customerProperties['sms_access']) {
                $this->sms_changes_allowed = true;
                return;
            }
        }

        $this->sms_changes_allowed = false;
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
                if ($this->resellerProperties['prepaid_changes']) {
                    $this->prepaid_changes_allowed = true;
                }
                return;
            } else if ($this->customerImpersonate == $this->loginCredentials['reseller']) {
                dprint("impersonate reseller");
                if ($this->resellerProperties['prepaid_changes']) {
                    $this->prepaid_changes_allowed = true;
                }
                return;
            }
        } elseif ($this->login_type == 'customer') {
            if ($this->resellerProperties['prepaid_changes'] && $this->customerProperties['prepaid_changes']) {
                $this->prepaid_changes_allowed = true;
                return;
            }
        }

        $this->prepaid_changes_allowed = false;
        return;
    }

    function getCustomerSettings () {
        dprint("getCustomerSettings()");
        if (!$this->loginCredentials['customer']) return;

        $id=$this->loginCredentials['customer'];

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
        dprint("getResellerSettings()");

        $this->logoFile         = $this->getFileTemplate("logo","logo");
        $this->headerFile       = $this->getFileTemplate("header.phtml");
        $this->footerFile       = $this->getFileTemplate("footer.phtml");
        $this->cssFile          = $this->getFileTemplate("main.css");

        if (!$this->reseller) {
            if ($this->pstn_access) {
                $this->availableGroups['free-pstn'] = array("Group"=>"free-pstn",
                                                    "WEBName" =>   sprintf(_("PSTN Access")),
                                                    "WEBComment"=> sprintf(_("Caller-ID")),
                                                    "SubscriberMayEditIt" => 0,
                                                    "SubscriberMaySeeIt"  => 1,
                                                    "ResellerMayEditIt"=>1,
                				                    "ResellerMaySeeIt"=>1
                                                    );
            }

            if ($this->sms_access) {
                $this->availableGroups['sms']  = array("Group"=>"sms",
                                                "WEBName" =>sprintf(_("Mobile SMS")),
                                                "SubscriberMayEditIt"=>0,
                                                "SubscriberMaySeeIt"=>1,
                                                "ResellerMayEditIt"=>0,
                                                "ResellerMaySeeIt"=>1
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

        if ($this->resellerProperties['billing_email']) {
            $this->billing_email         = $this->resellerProperties['billing_email'];
        }

        if (!$this->billing_email) {
            $this->billing_email=$this->support_email;
        }

        if ($this->resellerProperties['sip_settings_page']) {
            $this->sip_settings_page = $this->resellerProperties['sip_settings_page'];
        }

        if ($this->resellerProperties['blink_settings_page']) {
            $this->blink_settings_page = $this->resellerProperties['blink_settings_page'];
        }

        if ($this->resellerProperties['xcap_root']) {
            $this->xcap_root      = rtrim($this->resellerProperties['xcap_root'],'/');
            if ($this->append_domain_to_xcap_root) {
            	$this->xcap_root     .= "@".$this->domain."/";
            }
        }

        if ($this->resellerProperties['cdrtool_address']) {
            $this->cdrtool_address     = $this->resellerProperties['cdrtool_address'];
        }

        if ($this->resellerProperties['msrp_relay']) {
            $this->msrp_relay     = $this->resellerProperties['msrp_relay'];
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

        if (isset($this->resellerProperties['FNOL_access_number'])) {
            $this->FNOL_access_number = $this->resellerProperties['FNOL_access_number'];
        }

        if (isset($this->resellerProperties['payment_processor_class'])) {
            $this->payment_processor_class = $this->resellerProperties['payment_processor_class'];
        }

        if (isset($this->resellerProperties['change_privacy_access_number'])) {
            $this->change_privacy_access_number = $this->resellerProperties['change_privacy_access_number'];
        }

        if (isset($this->resellerProperties['check_privacy_access_number'])) {
            $this->check_privacy_access_number = $this->resellerProperties['check_privacy_access_number'];
        }

        if (isset($this->resellerProperties['reject_anonymous_access_number'])) {
            $this->reject_anonymous_access_number = $this->resellerProperties['reject_anonymous_access_number'];
        }

        if (isset($this->resellerProperties['absolute_voicemail_uri'])) {
            $this->absolute_voicemail_uri = $this->resellerProperties['absolute_voicemail_uri'];
        }

        if (isset($this->resellerProperties['pstn_access'])) {
            $this->pstn_access     = $this->resellerProperties['pstn_access'];
        }

        if (isset($this->resellerProperties['sms_access'])) {
            $this->sms_access     = $this->resellerProperties['sms_access'];
        }

        if ($this->pstn_access) {
            $this->availableGroups['free-pstn'] = array("Group"=>"free-pstn",
                                                "WEBName" =>   sprintf(_("PSTN Access")),
                                                "WEBComment"=> sprintf(_("Caller-ID")),
                                                "SubscriberMayEditIt" => "0",
                                                "SubscriberMaySeeIt"  => 1,
                                                "ResellerMayEditIt"=>1,
                                                "ResellerMaySeeIt"=>1
        										);
        }
        if ($this->sms_access) {
            $this->availableGroups['sms']  = array("Group"=>"sms",
                                            "WEBName" =>sprintf(_("Mobile SMS")),
                                            "SubscriberMayEditIt"=>0,
                                            "SubscriberMaySeeIt"=>1,
                                            "ResellerMayEditIt"=>0,
                                            "ResellerMaySeeIt"=>1
                                            );

        }
    }

    function getDiversions() {
        dprint("getDiversions()");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getCallDiversions($this->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

    	//print_r($result);

        reset($this->diversionType);
        foreach(array_keys($this->diversionType) as $condition) {
            $uri=$result->$condition;

            if ($uri == "<voice-mailbox>" && $this->absolute_voicemail_uri) {
                $uri = $this->voicemail['Account'];
            }

            if (preg_match("/^(sip:|sips:)(.*)$/i",$uri,$m)) {
                $uri=$m[2];
            }

            $this->diversions[$condition]=$uri;
        }

        //print_r($this->diversions);
    }

    function getDeviceLocations() {
        dprint("getDeviceLocations()");

        require_once($this->SipUAImagesFile);

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

        return array("name"  => sprintf (_("Voice Mailbox")),
                   "value" => $value,
                   "description"  => "Voicemail");
    }

    function showAboveTabs() {
        print "
        <tr>
        <td colspan=3>
        ";

        print "
        </td>
        </tr>
        ";

    }

    function showTabs() {
        print "
        <tr>
        ";
        print "
        <td colspan=3>
        ";

        print "<table border=0 width=100%>";
        print "<tr>";
        print "<td>";

        if ($this->isEmbedded()) {
            print $this->embedded_img;;
        }

        print "</td>";
        print "<td align=right>";
        print "
        <table border=0 cellspacing=0 cellpadding=0 align=right>
        <tr>
        ";
    
        $items=0;
    
        while (list($k,$v)= each($this->tabs)) {
            if ($this->tab==$k) {
                $_class='selected_tab';
            } else {
                $_class='tab';
            }
            print "<td class=$_class><a href=$this->url&tab=$k><font color=white>$v</font></a></td>";
        }
        print "
        </tr>
        </table>
        ";
        print "</td>";
        print "</tr>";
        print "</table>";
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

    function addInvoice() {
        // called after CC payment sucessfull
    }

    function showPaymentsTab() {
        $chapter=sprintf(_("Payments"));
        $this->showChapter($chapter);

        if (!$this->show_payments_tab) {
            return false;
        }

        if (!$this->owner) {

            print "
            <tr>
            <td colspan=3>
            <p>";

            print _("You must set the Owner to enable Credit Card Payments. ");

            print "
            </td>
            </tr>
            ";

            return false;
        }


        print "
        <tr>
        <td colspan=3>
        ";
        printf (_("Calling to PSTN numbers is possible at the costs set forth in the <a href=%s>price list</a>. "),$this->pstn_termination_price_page);
        print "
        </td>
        </tr>
        ";

		if (!$this->isEmbedded()) {
            if (!in_array("payments",$this->groups)) {
                $this->showIdentityProof();
                return false;
            }

            $this->showIdentityProof();
        }

        $this->getBalanceHistory();
 
        if (!count($this->balance_history)) {
            $this->first_transaction=true;
        } else {
            $this->first_transaction=false;
        }

        if (class_exists($this->payment_processor_class)) {
            $payment_processor = new $this->payment_processor_class(&$this);
        }

        if ($payment_processor->make_credit_checks) {
            // block account temporary to check the user
            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->removeFromGroup(array("username" => $this->username,"domain"=> $this->domain),"free-pstn");
        }
    }

    function showIdentityProof () {

        $max_file_size=1024000;

        $this->db = new DB_CDRTool();

        $chapter=sprintf(_("Proof of Identity"));
        $this->showChapter($chapter);

		if ($_REQUEST['task'] == 'upload') {
            if (!$_FILES['tmpfile']['tmp_name']) {
                print "<font color=red>";
                printf (_("Error: Please specify a file"));
                print "</font>";

            } else if (!$_REQUEST['name']) {
                print "<font color=red>";
                printf (_("Error: Please enter the name printed on the Credit Card"));
                print "</font>";

            } else if (!preg_match("/^\d{4}$/",$_REQUEST['last_digits'])) {
                print "<font color=red>";
                printf (_("Error: Last digits must be numeric"));
                print "</font>";

            } else if (!preg_match("/^\+[1-9][0-9]{7,14}$/",$_REQUEST['mobile_number'])) {

                print "<font color=red>";
                printf (_("Error: Mobile Number must be in international format starting with +"));
                print "</font>";

            } else if ($_FILES['tmpfile']['size']['size'] > $max_file_size) {
                print "<font color=red>";
                printf (_("Error: Maximum file size is %s"),$max_file_size);
                print "</font>";

            } else {

                $fp=fopen($_FILES['tmpfile']['tmp_name'], "r");
                $content=fread($fp, $_FILES['tmpfile']['size']);
                fclose($fp);
    
                $query=sprintf("insert into subscriber_docs (
                `name`,
                `username`,
                `domain`,
                `document`,
                `file_content`,
                `file_name`,
                `file_size`,
                `file_type`,
                `file_date`,
                `last_digits`,
                `mobile_number`
                ) values (
                '%s',
                '%s',
                '%s',
                'identity',
                '%s',
                '%s',
                '%s',
                '%s',
                NOW(),
                '%s',
                '%s'
                )",
                addslashes($_REQUEST['name']),
                $this->username,
                $this->domain,
                addslashes($content),
                addslashes($_FILES['tmpfile']['name']),
                addslashes($_FILES['tmpfile']['size']),
                addslashes($_FILES['tmpfile']['type']),
                addslashes($_REQUEST['last_digits']),
                addslashes($_REQUEST['mobile_number'])
                );

                if (!$this->db->query($query)) {
                    print "<font color=red>";
                    printf ("Error: Failed to save identity document %s (%s)", $this->db->Error,$this->db->Errno);
                    print "</font>";
                }

                // send mail
                include_once('Mail.php');
                include_once('Mail/mime.php');

                $subject=sprintf ("%s requested CC Payments",$this->account);

                $hdrs = array(
                     'From'    => $this->email,
                     'Subject' => $subject
                     );

                $crlf = "\n";
                $mime = new Mail_mime($crlf);

                $mime->setTXTBody($subject);
                $mime->setHTMLBody($subject);

                $mime->addAttachment($content, $_FILES['tmpfile']['type'],$_FILES['tmpfile']['name'],'false');

                $body = $mime->get();     
                $hdrs = $mime->headers($hdrs);

                $mail =& Mail::factory('mail');

                $mail->send($this->billing_email, $hdrs, $body);
            }
        }

  		if ($this->login_type != 'subscriber' && $_REQUEST['task'] == 'delete_identity_proof' && $_REQUEST['confirm']) {
            $query=sprintf("delete from subscriber_docs
            where username = '%s'
            and domain = '%s'
            and document = 'identity'",
            $this->username,
            $this->domain
            );
    
            if (!$this->db->query($query)) {
                print "<font color=red>";
                printf ("Error deleting record: %s (%s)", $this->db->Error,$this->db->Errno);
                print "</font>";
            }
        }

    	$query=sprintf("select * from subscriber_docs
        where username = '%s'
        and domain = '%s'
        and document = 'identity'",
        $this->username,
        $this->domain
        );

        if (!$this->db->query($query)) {
            print "<font color=red>";
            printf ("Error for database query: %s (%s)", $this->db->Error,$this->db->Errno);
            print "</font>";
        }

        if ($this->db->num_rows()) {

            print "
            <tr>
            <td colspan=3>
            ";

	        if (!in_array("payments",$this->groups)) {
                print "<p>";
                print _("Credit Card payments will be activated after your identity is verified. ");
            }

            print "<p>";
            print "<table border=0>
            ";

            printf ("<tr bgcolor=lightgrey><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
            _("Name"),
            _("Document"),
            _("Type"),
            _("Size"),
            _("Date"),
            _("Last digits"),
            _("Mobile Number")
            );

			if ($this->login_type != 'subscriber') {
                print "<td>";
                print _("Actions");
                print "</td>";
            }

            printf ("</tr>");

            $this->db->next_record();

            $download_url=$this->url.'&action=export_identity_proof';

            printf ("<tr> <td>%s</td><td><a href=%s>%s</a></td> <td>%s</td> <td>%s KB</td> <td>%s</td><td align=right>%s</td><td align=right>%s</td>",
            $this->db->f('name'),
            $download_url,
            $this->db->f('file_name'),
            $this->db->f('file_type'),
            number_format($this->db->f('file_size')/1024,2),
            $this->db->f('file_date'),
            $this->db->f('last_digits'),
            $this->db->f('mobile_number')
            );

			if ($this->login_type != 'subscriber') {
                if ($_REQUEST['task'] == 'delete_identity_proof' && !$_REQUEST['confirm']){
                    $delete_url=$this->url.'&tab=payments&task=delete_identity_proof&confirm=1';
                    printf ("<td align=right><a href='%s'>%s</a></td>",$delete_url,_("Confirm"));
                } else {
                    $delete_url=$this->url.'&tab=payments&task=delete_identity_proof';
                    printf ("<td align=right><a href='%s'>%s</a></td>",$delete_url,$this->delete_img);
                }
            }

            printf ("</tr>");

            print "
            </table>
            </td>
            </tr>
            ";

        } else {
            print "
            <tr>
            <td colspan=3>
            <p>";

            print _("Credit Card payments are available only to verified customers. ");

            print "<p>";
            printf (_("To become verified, upload a copy of your passport or driving license that matches the Credit Card owner. "),$this->billing_email, $this->account, $this->billing_email);

            print "
            </td>
            </tr>
            ";

            print "
            <form action=$this->url method='post' enctype='multipart/form-data'>
            <input type='hidden' name='tab' value='payments'>
            <input type='hidden' name='task' value='upload'>
            <input type='hidden' name='MAX_FILE_SIZE' value=$max_file_size>
            ";
    
            print "
            <tr class=even>
            <td>";
            print _("Name printed on the Credit Card");
            print "
            </td>
            <td colspan=2>
            ";
            printf ("<input type=text size=35 name='name' value='%s'>",$_REQUEST['name']);
            print "
            </td>
            </tr>
            ";
    
            print "
            <tr class=odd>
            <td>";
            print _("Scanned copy of your Passport or Driver License");
            print "
            </td>
            <td colspan=2>
            ";
            printf ("<input type='file' name='tmpfile'>");
            print "
            </td>
            </tr>
            ";

            print "
            <tr class=even>
            <td>";
            print _("Mobile Number");
            print "
            </td>
            <td colspan=2>
            ";
            printf("<input type=text size=15 name='mobile_number' value='%s'> %s",$_REQUEST['mobile_number'],_("International format starting with +"));
    
            print "
            </td>
            </tr>
            ";

            print "
            <tr class=even>
            <td>";
            print _("Last 4 digits on your Credit Card");
            print "
            </td>
            <td colspan=2>
            ";
            printf("<input type=text size=5 name='last_digits' value='%s'>",$_REQUEST['last_digits']);
    
            print "
            </td>
            </tr>
            ";
    
            print "
            <tr class=odd>
            <td colspan=3>
            ";
            print "
            <input type=submit value=";
            print _("Save");
            print ">
            </td>
            </tr>
            </form>
            ";

        }
    }

    function exportIdentityProof() {

        $this->db = new DB_CDRTool();

    	$query=sprintf("select * from subscriber_docs
        where username = '%s'
        and domain = '%s'
        and document = 'identity'",
        $this->username,
        $this->domain
        );

        if (!$this->db->query($query)) {
            print "<font color=red>";
            printf ("Error for database query: %s (%s)", $this->db->Error,$this->db->Errno);
            print "</font>";
        }

        if ($this->db->num_rows()) {
            $this->db->next_record();

			$h=sprintf("Content-type: %s",$this->db->f('file_type'));
            Header($h);

            $h=sprintf("Content-Disposition: attachment; filename=%s",$this->db->f('file_name'));
            Header($h);

            $h=sprintf("Content-Length: %s",$this->db->f('file_size'));
            Header($h);

            $this->db->p('file_content');
        }
    }

    function showIdentityTab() {
        $this->getEnumMappings();
        $this->getAliases();

        $chapter=sprintf(_("SIP Account"));
        $this->showChapter($chapter);

        print "
        <tr class=even>
        <td>";
        print _("SIP Address");
        print "
        </td>
        <td>sip:$this->account
        </td>
        </tr>
        ";

        /*
        print "
        <tr>
        <td>";
        print _("Full Name");
        print "
        </td>
        <td>$this->fullName
        </td>
        </tr>
        ";
        */

        print "
        <tr class=odd>
        <td>";
        print _("Username");
        print "
        </td>
        <td>$this->username
        </td>
        </tr>
        ";
        print "
        <tr class=even>
        <td>";
        print _("Domain/Realm");
        print "
        </td>
        <td>$this->domain
        </td>
        </tr>
        ";

        print "
        <tr class=odd>
        <td>";
        print _("Outbound Proxy");
        print "
        </td>
        <td>$this->sip_proxy
        </td>
        </tr>
        ";

        if ($this->presence_engine) {
            print "
            <tr class=even>
                <td>";
                print _("XCAP Root");
                print "
                </td>
                <td>$this->xcap_root
            </td>
            </tr>
            ";
        }

		if ($this->pstn_access && $this->rpid) {
            $chapter=sprintf(_("PSTN"));
            $this->showChapter($chapter);

            print "
            <tr>
              <td>";
              print _("Caller-ID");
              print "</td>
              <td>$this->rpid</td>
            </tr>
            ";
        }

        $t=0;
        foreach($this->enums as $e)  {
            $t++;

            $rr=floor($t/2);
            $mod=$t-$rr*2;
    
            if ($mod ==0) {
                $_class='odd';
            } else {
                $_class='even';
            }

            print "
            <tr class=$_class>

              <td>";
              print _("Phone Number");
              print "</td>
              <td>$e</td>
            </tr>
            ";
        }

 
        $chapter=sprintf(_("Aliases"));
        $this->showChapter($chapter);
 
        print "
        <tr>
        <td colspan=2>";
        printf (_("You may create new aliases under the same domain"));
        printf ("
        </td>
        </tr>
        ");
 
        $t=0;
 
        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";
 
        foreach($this->aliases as $a)  {
            $t++;
 
            $rr=floor($t/2);
            $mod=$t-$rr*2;
    
            if ($mod ==0) {
                $_class='odd';
            } else {
                $_class='even';
            }
 
            print "
            <tr class=$_class>
 
              <td>";
                print _("SIP Alias");
                print "
              </td>
              <td> <input type=text size=35 name=aliases[] value=\"$a\"></td>
            </tr>
            ";
        }
 
        print "
        <tr>
          <td>";
            print _("New SIP Alias");
            print "
          </td>
          <td> <input type=text size=35 name=aliases[]></td>
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

        if (!$this->isEmbedded()) {

            if ($this->enrollment_url) {
                include("/etc/cdrtool/enrollment/config.ini");
    
                if (is_array($enrollment)) {
                    $chapter=sprintf(_("TLS Certificate"));
                    $this->showChapter($chapter);
                    if ($this->sip_settings_api_url) {
                        /*
                        print "
                        <tr>
                        <td colspan=2>";
                        printf (_("The certificate is used for accessing <a href=%s target=sip_api>SIP Settings API</a>"),$this->sip_settings_api_url);
                        printf ("
                        </td>
                        </tr>
                        ");
                        */
                    }
    
                    print "
                    <tr>
                    <td>";
                    print _("X.509 Format");
                    printf ("
                    </td>
                    <td><a href=%s&action=get_crt>%s.crt</a>
                    </td>
                    </tr>
                    ",$this->url, $this->account);

                    /*
                    print "
                    <tr>
                    <td>";
                    print _("PKCS#12 store format");
                    printf ("
                    </td>
                    <td><a href=%s&action=get_p12>%s.p12</a>
                    </td>
                    </tr>
                    <tr>
                      <td height=3 colspan=2></td>
                    </tr>",$this->url, $this->account);
                    */

                }
            }
        }

        if ($this->enable_msn) {
            $chapter=sprintf(_("MSN"));
            $this->showChapter($chapter);

            list($msn_address,$msn_password)=explode(":",$this->Preferences['msn_credentials']);

            print "
            <tr>
            <td colspan=2>";
            printf (_("You can connect to MSN subscribers by providing your MSN credentials"));
            printf ("
            </td>
            </tr>
            ");
     
            $t=0;
     
            print "
            <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
            ";

            print "
            <tr class=even>
            <td>";
                print _("Address");
                printf("
              </td>
              <td> <input type=text size=35 name=msn_address value='%s'></td>
            </tr>
            ",$msn_address);
    
            print "
            <tr class=odd>
            <td>";
                print _("Password");
                printf ("
              </td>
              <td> <input type=password size=35 name=msn_password value='%s'></td>
            </tr>
            ",$msn_password);
            print "
            <tr>
              <td align=left>
            <input type=hidden name=action value=\"set msn\">
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

        if ($this->enable_yahoo) {
            $chapter=sprintf(_("Yahoo! Messenger"));
            $this->showChapter($chapter);

            list($yahoo_address,$yahoo_password)=explode(":",$this->Preferences['yahoo_credentials']);

            print "
            <tr>
            <td colspan=2>";
            printf (_("You can connect to Yahoo subscribers by providing your Yahoo credentials"));
            printf ("
            </td>
            </tr>
            ");
     
            $t=0;
     
            print "
            <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
            ";
     
            print "
            <tr class=even>
            <td>";
                print _("Address");
                printf ("
              </td>
              <td> <input type=text size=35 name=yahoo_address value='%s'></td>
            </tr>
            ",$yahoo_address);
    
            print "
            <tr class=odd>
            <td>";
                print _("Password");
                printf ("
              </td>
              <td> <input type=password size=35 name=yahoo_password value='%s'></td>
            </tr>
            ",$yahoo_password);
            print "
            <tr>
              <td align=left>
            <input type=hidden name=action value=\"set yahoo\">
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

        if (!$this->isEmbedded()) {

            if ($this->enrollment_url) {
                include("/etc/cdrtool/enrollment/config.ini");
    
                if (is_array($enrollment)) {
                    $chapter=sprintf(_("TLS Certificate"));
                    $this->showChapter($chapter);
                    if ($this->sip_settings_api_url) {
                        /*
                        print "
                        <tr>
                        <td colspan=2>";
                        printf (_("The certificate is used for accessing <a href=%s target=sip_api>SIP Settings API</a>"),$this->sip_settings_api_url);
                        printf ("
                        </td>
                        </tr>
                        ");
                        */
                    }
    
                    print "
                    <tr>
                    <td>";
                    print _("X.509 Format");
                    printf ("
                    </td>
                    <td><a href=%s&action=get_crt>%s.crt</a>
                    </td>
                    </tr>
                    ",$this->url, $this->account);

                    /*
                    print "
                    <tr>
                    <td>";
                    print _("PKCS#12 store format");
                    printf ("
                    </td>
                    <td><a href=%s&action=get_p12>%s.p12</a>
                    </td>
                    </tr>
                    <tr>
                      <td height=3 colspan=2></td>
                    </tr>",$this->url, $this->account);
                    */

                }
            }
        }

        print "
        <form method=post>";

        print "
        <tr>
          <td align=left colspan=2>
        ";

        if ($this->email) {
            printf (_("Email SIP Account information to %s"),$this->email);
            print "
            <input type=hidden name=action value=\"send email\">
            <input type=submit value=";
            print _("Send");
            print ">";
        }

        if ($this->sip_settings_page && $this->login_type != 'subscriber') {
            print "<p>";
            printf (_("Login using SIP credentials at <a href=%s>%s</a>"),$this->sip_settings_page,$this->sip_settings_page);
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

    function showDownloadTab() {

        $chapter=sprintf(_("SIP Client"));
        $this->showChapter($chapter);

        print "
        <tr class=odd>
        <td colspan=2>";

        $this->render_download_applet();

        print "
        </td>
        </tr>
        ";
    }

    function showSupportTab() {

        $chapter=sprintf(_("Support"));
        $this->showChapter($chapter);

        print "
        <tr class=odd>
        <td colspan=2>";

        print "<p>";
        printf (_("To request support you may send an e-mail to %s"),$this->support_email);

        if ($this->support_web) {
            print "<p>";
			printf (_("For more information visit %s"),$this->support_web);
        }

        print "
        </td>
        </tr>
        ";
    }

    function render_download_applet() {

		$this->valid_os=array('nt','mac');

        require("browser.php");
        $os=browser_detection('os');

        if ($_passport = $this->generateCertificate()) {
        	$_account['passport']       = $_passport;
        }

        $_account['sip_address']    = $this->account;
        $_account['password']       = $this->password;
        $_account['email']          = $this->email;
        $_account['outbound_proxy'] = $this->sip_proxy;
        $_account['xcap_root']      = $this->xcap_root;
        $_account['msrp_relay']     = $this->msrp_relay;
        $_account['settings_url']   = $this->blink_settings_page;

        print "<table border=0>";

        if (in_array($os,$this->valid_os)) {
        	print "<tr><td>";

            printf (_("Download and install <a href=http://icanblink.com target=blink>Blink</a> preconfigured with your SIP account:"));

	        print "</td></tr>";

        	print "<tr><td>";
            printf ("<applet code='com.agprojects.apps.browserinfo.BlinkConfigure' archive='blink_download.jar?version=%s' name='BlinkDownload' height='35' width='250' align='left'>
            <param name='label_text' value='Download Blink'>
            <param name='click_label_text' value='Downloading...'>
            <param name='download_url' value='%s'>
            <param name='file_name' value=''>
            <param name='file_content' value='%s'>
            </applet>",
            rand(),
            $this->blink_download_url,
            urlencode(json_encode($_account))
            );
        	print "</td></tr>";

        } else {
        	print "<tr><td>";

            print "<p>";
            printf (_("To download Blink visit <a href='%s' target=blink>%s</a>"),'http://icanblink.com/download.phtml','http://icanblink.co/download.phtml');
        	print "</td></tr>";
        }

        print "<tr><td>";

        printf (_("If you have already installed Blink, you can configure it to use your SIP account:"));

        print "</td></tr>";
        print "<tr><td>";

        printf ("<applet code='com.agprojects.apps.browserinfo.BlinkConfigure' archive='blink_download.jar?version=%s' name='BlinkConfigure' height='35' width='250' align='left'>
        <param name='label_text' value='Configure Blink with this account'> 
        <param name='click_label_text' value='Please restart Blink now!'>
        <param name='download_url' value=''> 
        <param name='file_name' value=''> 
        <param name='file_content' value='%s'> 
        </applet>",
        rand(),
        urlencode(json_encode($_account))
        );

        print "</td></tr>";
        print "</table>";

        print "<p>";
        printf ("Notes. ");
        print _("<a href='http://www.java.com/en/download/manual.jsp'>Java Runtime Environment</a> (JRE) must be activated in the web browser. ");
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

    function showSettingsTab() {

        $this->getVoicemail();

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        $chapter=sprintf(_("SIP Account"));
        $this->showChapter($chapter);

        if ($this->login_type != "subscriber" ) {
    
            print "
            <tr class=even>
            <td>";
            print _("First Name");
            print "
            </td>
            <td>";
    
            print "<input type=text size=15 name=first_name value=\"$this->firstName\">";
    
            print "
            </td>
            </tr>
            ";
    
            print "
            <tr class=odd>
            <td>";
            print _("Last Name");
            print "
            </td>
            <td>";
    
            print "<input type=text size=15 name=last_name value=\"$this->lastName\">";
    
            print "
            </td>
            </tr>
            ";

        }

        print "
        <tr class=even>
        <td>";
        print _("Password");
        print "
        </td>
        <td>";

        print "<input type=text size=15 name=sip_password>";
        print _("Enter text to change the current password");
        printf ("\n\n<!-- \nSIP Account password: %s\n -->\n\n",$this->password);

        print "
        </td>
        </tr>
        ";

        print "
        <tr class=odd>
        <td>";
        print _("Language");
        print "
        </td>
        <td>";

        print "
        <select name=language>
        ";

        $selected_lang[$this->Preferences['language']]="selected";

        foreach (array_keys($this->languages) as $_lang) {
             printf ("<option value='%s' %s>%s\n",$_lang,$selected_lang[$_lang],$this->languages[$_lang]['name']);
        }

        print "
        </select>
        ";

        print "
        <tr class=even>
        <td>";
        print _("Timezone");
        print "
        </td>
        <td>
        ";
        $this->showTimezones('timezone',$this->timezone);
        print " ";
        $timestamp=time();
        $LocalTime=getLocalTime($this->timezone,$timestamp);
        print _("Local Time");
        print ": $LocalTime";
        //dprint_r($this->availableGroups);
        print "
        </td>
        </tr>
        ";

        if (count($this->emergency_regions) > 0) {
            print "
            <tr>
            <td>";
            print _("Location");
            print "
            </td>
            <td>
            ";
            print "<select name=region>";
            $selected_region[$this->region]="selected";
            foreach (array_keys($this->emergency_regions) as $_region) {
                printf ("<option value=\"%s\" %s>%s",$_region,$selected_region[$_region],$this->emergency_regions[$_region]);
            }
            print "</select>";

            print "
            </td>
            </tr>
            ";
        }

        if (in_array("free-pstn",$this->groups)) {
 
            if (in_array("quota",$this->groups)) {
                $_class="orange";
            } else {
                $_class="";
            }

            if ($this->pstn_changes_allowed) {
                print "
                <tr class=$_class>
                <td>";
                print _("Quota");
                print "
                </td>
                <td>
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
                    print _("Un-block");
                    print "
                    <input type=checkbox name=quota_deblock value=1>
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
                <td>";
                print _("Quota");
                print "
                </td>
                <td class=$td_class>
                <table cellspacing=0 cellpadding=0 width=100%>
                <tr>
                <td>";
                printf ("%s %s",$this->quota,$this->currency);
                $this->getCallStatistics();
                if ($this->thisMonth['price']) {
                    print "&nbsp;&nbsp;&nbsp;";
                    printf (_("This month usage: %.2f %s"),$this->thisMonth['price'], $this->currency);
                    printf (" / %d ",$this->thisMonth['calls']);
                    print _("Calls");
                }

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
            <tr>
            <td valign=top>";
            print _("Prepaid");
            print "</td>
            <td>
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

            if ($this->login_type == 'reseller' && !$this->availableGroups[$key]['ResellerMaySeeIt']) {
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
            } elseif ($this->login_type == 'reseller') {
                if ($this->availableGroups[$key]['ResellerMayEditIt']) {
                    $disabled_box = "";
                } else {
                    $disabled_box = "disabled=true";
                }
            }

            if ($key=="free-pstn" && !$this->pstn_changes_allowed) {
                $disabled_box   = "disabled=true";
            }

            if ($key=="blocked" && $checked_box[$key]) {
                $_class="orange";
            } else {
                $_class="";
            }

            print "
            <tr class=$_class>
            <td valign=top>$elementName</td>
            <td>
            ";

            if ($key=="blocked") {
                if ($this->Preferences['blocked_by']) {
                    $selected_blocked_by[$this->Preferences['blocked_by']]='selected';
                } else if ($checked_box[$key]) {
                    $selected_blocked_by['reseller']='selected';
                }

                if ($this->login_type == 'admin' || $this->login_type == 'reseller') {

                    if ($this->customer != $this->reseller || $selected_blocked_by['customer']) {
                        printf ("
                        <select name=%s>
                        <option value=''>Active
                        <option value='customer' %s> %s (%d)
                        <option value='reseller' %s> %s (%d)
                        </select>
                        ",
                        $key,
                        $selected_blocked_by['customer'],
                        _("Blocked by Customer"),
                        $this->customer,
                        $selected_blocked_by['reseller'],
                        _("Blocked by Reseller"),
                        $this->reseller
                        );
                    } else if ($this->reseller) {
                        printf ("
                        <select name=%s>
                        <option value=''>%s
                        <option value='reseller' %s> %s (%d)
                        </select>
                        ",
                        $key,
                        _("Active"),
                        $selected_blocked_by['reseller'],
                        _("Blocked by Reseller"),
                        $this->reseller
                        );
                    } else {
                        printf ("
                        <select name=%s>
                        <option value=''>%s
                        <option value='reseller' %s> %s
                        </select>
                        ",
                        $key,
                        _("Active"),
                        $selected_blocked_by['reseller'],
                        _("Blocked")
                        );
                    }

                } else if ($this->login_type == 'customer' ) {

                    if (in_array($key,$this->groups)) {
                       if ($this->Preferences['blocked_by'] != 'reseller') {
                           printf ("
                           <select name=%s>
                           <option value=''>%s
                           <option value='customer' %s> %s
                           </select>
                           ",
                           $key,
                           _("Active"),
                           $selected_blocked_by['customer'],
                           _("Blocked")
                           );
                       } else {
                           print _("Blocked by Reseller");
                       }
                   } else {
                       printf ("
                       <select name=%s>
                       <option value=''>%s
                       <option value='customer' %s> %s
                       </select>
                       ",
                       $key,
                       _("Active"),
                       $selected_blocked_by['customer'],
                       $selected_blocked_by['reseller'],
                       _("Blocked")
                       );
                   }
                } else {
                       if (in_array($key,$this->groups)) {
                           print _("Blocked");
                    } else {
                           print _("Active");
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

        $this->showQuickDial();

        $this->showMobileNumber();

        print "
        <tr class=even>
        <td>";
        print _("No-answer Timeout");
        printf ("
        </td>
        <td align=left>
        ");
 
        printf ("<input name=timeout value='%d' size=3> s",$this->timeout);
 
        print "
        </td>
        </tr>
        ";

        print "
        <tr class=odd>
        <td>";
        print _("Tabs");
        print "
        </td>
        <td>";

		if ($this->show_presence_tab) {
            if ($this->Preferences['show_presence_tab']){
                $check_show_presence_tab="checked";
            } else {
                $check_show_presence_tab="";
            }
            printf ("<input type=checkbox %s value=1 name='show_presence_tab'>%s\n",$check_show_presence_tab,_("Presence"));
        }

        if (in_array("free-pstn",$this->groups)) {

            if ($this->Preferences['show_barring_tab']){
                $check_show_barring_tab="checked";
            } else {
                $check_show_barring_tab="";
            }
    
            printf ("<input type=checkbox %s value=1 name='show_barring_tab'>%s\n",$check_show_barring_tab,_("Barring"));
        }

        print "
        </td>
        </tr>
        ";

        $this->showVoicemail();

        $this->showBillingProfiles();

        $chapter=sprintf(_("Notifications Address"));
        $this->showChapter($chapter);

        print "
        <tr class=even>
          <td>";
            print _("Email Address");
            print "
          </td>
          <td align=left>
            <input type=text size=40 maxsize=255 name=mailto value=\"$this->email\">
          </td>
        </tr>
        ";

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"save settings\">
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

    function showDiversionsTab () {

        $this->getVoicemail();
        $this->getDivertTargets();
        $this->getDiversions();

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        $chapter=sprintf(_("Call Forwarding"));
        $this->showChapter($chapter);

        $this->showDiversions();

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set diversions\">
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

        $chapter=sprintf(_("Voice Mailbox"));
        $this->showChapter($chapter);

        print "
        <tr class=even>
        <td>";
        print _("Enable");
        print "</td>
        <td>
        <input type=checkbox value=1 name=voicemail $checked_voicemail $disabled_box>
        ";

        if ($this->voicemail['Account'] &&
            ($this->login_type == 'admin' || $this->login_type == 'reseller')) {
            print " (";
            print _("Mailbox");
            printf (" %s) ",$this->voicemail['Account']);
        }

        print "
        </td>
        </tr>
        ";

        if ($this->voicemail['Account']) {

            print "
            <tr class=odd>
            <td>";

            print _("Delivery");
            print "</td>
            <td>
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
                $_text=sprintf(_("Send voice messages by e-mail to %s"),$this->email);
                printf ("<option value=1 %s>%s",$selected_store_voicemail['email'],$_text);
                printf ("<option value=0 %s>%s",$selected_store_voicemail['server'],_("Send messages by e-mail and store messages on the server"));
                print "</select>";
            } else {
                printf (_("Voice messages are sent by email to %s"),$this->email);
            }

            print "
            </td>
            </tr>
            ";

            if (!$this->voicemail['DisableOptions']) {

                print "
                <tr class=even>
                <td>";
    
                print _("Password");
                print "</td>
                <td>
                ";
    
                printf ("<input type=text size=15 name=voicemail_password value=\"%s\">",$this->voicemail['Password']);
    
                print "
                </td>
                </tr>
                ";
    
                if ($this->voicemail_access_number) {
                   print "
                   <tr>
                   <td colspan=2>";
                   printf(_("Dial %s to listen to your messages or change preferences. "),$this->voicemail_access_number);
                   print "</td>
                   </tr>
                   ";
                }
            }
        }
    }

    function showOwner() {
        //if ($this->login_type == 'subscriber') return true;

        if ($this->pstn_changes_allowed) {
            print "
            <tr>
            <td>";
            print _("Owner");
            print "</td>
            <td>
            <input type=text name=owner size=7 value=\"$this->owner\">
            ";
            print "
            </td>
            </tr>
            ";
        } else {
            print "
            <tr>
            <td>";
            print _("Owner");
            print "</td>
            <td>
            $this->owner
            ";
            print "
            </td>
            </tr>
            ";
        }
    }

    function showDevicesTab() {
        $this->getDeviceLocations();

        if (count($this->locations)) {
            $chapter=sprintf(_("Registered SIP Devices"));
            $this->showChapter($chapter);

            $j=0;

            foreach (array_keys($this->locations) as $location) {
                $j++;
                $contact       = $this->locations[$location]['contact'];
                $publicContact = $this->locations[$location]['publicContact'];
                $expires       = normalizeTime($this->locations[$location]['expires']);
                $user_agent    = $this->locations[$location]['user_agent'];
                $transport     = $this->locations[$location]['transport'];
                $UAImage       = $this->getImageForUserAgent($user_agent);

                $rr=floor($j/2);
                $mod=$j-$rr*2;
        
                if ($mod ==0) {
                    $_class='odd';
                } else {
                    $_class='even';
                }
    
                print "
                <tr class=$_class>";

                print "<td align=center>";
                printf ("<img src='%s/30/%s' border=0>",$this->SipUAImagesPath,$UAImage);
                print "</td>";
                print "<td align=left>";
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
                            print " ($publicContact) ";
                        }

                        if ($publicContact) {
                            $_els=explode(":",$publicContact);
                            if ($_loc=geoip_record_by_name($_els[0])) {
        						$this->geo_location=$_loc['country_name'].'/'.$_loc['city'];
        					} else if ($_loc=geoip_country_name_by_name($_els[0])) {
        						$this->geo_location=$_loc;
        					} else {
        						$this->geo_location='';
        					}
                            printf ("%s",$this->geo_location);
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

        $this->barring_prefixes=$result;
        return true;
    }

    function setBarringPrefixes() {
        dprint("setBarringPrefixes");
        $prefixes=array();
        $barring_prefixes=$_REQUEST['barring_prefixes'];

        foreach ($barring_prefixes as $_prefix) {
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

    function showBarringTab() {
        $chapter=sprintf(_("Barred Destinations"));
        $this->showChapter($chapter);

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        print "
        <tr>
        <td colspan=2 align=left>";
        print _("You can deny outbound calls to unwanted PSTN prefixes. ");
        print "<p>";
        print "
        </td>
        </tr>
        ";

        print "
        <tr>
        <td align=left>";
        print _("Destination Prefix");
        print "</td>";
        print "<td align=left>
        <input type=text name=barring_prefixes[]>
        ";
        print _("Example");
        print ": +31900";
        print "
        </td>
        </tr>
        ";

        if ($this->getBarringPrefixes()) {
            foreach ($this->barring_prefixes as $_prefix) {
                $found++;

                $rr=floor($found/2);
                $mod=$found-$rr*2;
        
                if ($mod == 0) {
                    $_class='odd';
                } else {
                    $_class='even';
                }

                print "
                <tr class=$_class>";

                print "<td align=left>";
                print _("Destination Prefix");
                print "</td>";
                print "<td align=left>
                <input type=text name=barring_prefixes[] value=\"$_prefix\">
                </td>";
                print "<tr>";
            }
        }

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set barring\">
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

    function saveSettings() {

        $this->getVoicemail();

        /*
        $this->getEnumMappings();
        $this->getDivertTargets();
        $this->getDiversions();
        */

        foreach ($this->form_elements as $el) {
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

        $this->availableGroups['voicemail']=array("Group"=>"voicemail",
                                    "WEBName" =>sprintf (_("Voice Mailbox")),
                                    "SubscriberMayEditIt"=>"1",
                                    "SubscriberMaySeeIt"=>0
                                    );

        if (!$this->voicemail['Account'] && $voicemail) {
            if ($this->addVoicemail()) {
                $this->setVoicemailDiversions();
                $this->createdVoicemailnow=1;
            }
        } else if ($this->voicemail['Account'] && !$voicemail) {
            if ($this->deleteVoicemail()) {
                $this->voicemail['Account']="";
                $this->removeVoicemailDiversions();
            }
        }

        if ($this->pstn_changes_allowed) {
            if (strcmp($quota,$this->quota) != 0) {
                if (!$quota) $quota=0;
                $result->quota=intval($quota);
                dprint ("change the quota");
                $this->somethingChanged=1;
            }

            if ($quota_deblock) {
                $result->groups = array_unique(array_diff($this->groups,array('quota')));
                $this->somethingChanged=1;

                $this->SipPort->addHeader($this->SoapAuth);
                $this->SipPort->removeFromGroup(array("username" => $this->username,"domain"=> $this->domain), "quota");
            }

			$rpid=trim($rpid);
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

                        $this->somethingChanged=1;
                    } else if (!in_array($key,$this->groups) && $val) {
                    	if (!$this->prepaid_changes_allowed) {
                            $this->somethingChanged=1;
                            $result->prepaid=1;
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
                } else if ($key == 'sms') {
                    if ($this->sms_changes_allowed) {
    
                        if (!$val && in_array($key,$this->groups)) {
                            $this->somethingChanged=1;
                        } else if ($val && !in_array($key,$this->groups)) {
                            $this->somethingChanged=1;
                        }
    
                        if ($val) $newACLarray[]=trim($key);
                     } else {
                        // copy old setting if exists
                        if (in_array($key,$this->groups)) {
                            $newACLarray[]=trim($key);
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
                //print("Set lang $language");
                $this->changeLanguage($language);
            }

            $this->setPreference("language",$language);
            $this->somethingChanged=1;
        }

        if ($show_presence_tab != $this->Preferences['show_presence_tab'] ) {
            $this->setPreference("show_presence_tab",$show_presence_tab);
            $this->somethingChanged=1;
        }

        if ($show_barring_tab != $this->Preferences['show_barring_tab'] ) {
            $this->setPreference("show_barring_tab",$show_barring_tab);
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

        if (strcmp($quickdial,$this->quickdial) != 0) {
            if ($this->SOAPversion > 1) {
                $result->quickdialPrefix=$quickdial;
            } else {
                $result->quickdial=$quickdial;
            }

            $this->somethingChanged=1;
        }

        $mobile_number  = preg_replace("/[^\+0-9]/","",$mobile_number);
        if ($mobile_number && !preg_match("/^\+/",$mobile_number)) {
            $mobile_number='+'.$mobile_number;
        }

        if ($this->Preferences['mobile_number'] != $mobile_number) {
            $this->setPreference('mobile_number',$mobile_number);
            $this->somethingChanged=1;
        }

        if (!$this->createdVoicemailnow) {
            // moved to its own tab
            //$this->setDiversions();
        }

        if ($this->timeoutWasNotSet || $timeout != $this->timeout) {
            $this->somethingChanged=1;
            if ($this->SOAPversion > 1) {
                $result->timeout=intval($timeout);
            } else {
                $result->answerTimeout=intval($timeout);
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

        $this->getVoicemail();
        $this->getEnumMappings();

        $this->getDivertTargets();
        $this->getDiversions();

        foreach (array_keys($this->diversionType) as $condition) {
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

            if ($this->diversions[$condition]) {

                if ($uri_description=='Disabled' && $this->CallPrefUriType[$condition]!='Disabled') {
                    $diversions[$condition]="";
                } else if ($uri_description != 'Disabled' && $selectedURI) {
                    if (checkURI($selectedURI)) {
                        if ($this->CallPrefUriType[$condition]=='Disabled') {
                            $diversions[$condition]="";
                        } else {
                            if ($this->diversions[$condition] != $uri) {
                                $diversions[$condition]=$uri;
                            } else {
                                $diversions[$condition]=$this->diversions[$condition];
                            }
                        }

                    } else {
                           $diversions[$condition]=$this->diversions[$condition];
                        dprint("Failed to check address $selectedURI");
                    }
                }
            } else if ($uri_description!='Disabled' && $selectedURI) {
                if (checkURI($selectedURI)) {
                    $diversions[$condition]=$uri;
                   } else {
                       dprint("Failed to check address $condition=\"$selectedURI\"");
                       $diversions[$condition]=$this->diversions[$condition];
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

        foreach(array_keys($this->diversions) as $key) {
            if ($this->diversions[$key] != $diversions[$key]) {
                //$log=sprintf("Diversion %s changed from %s to %s",$key,htmlentities($this->diversions[$key]),htmlentities($diversions[$key]));
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
            } else {
            	$this->diversions=$diversions;
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

        reset($this->diversionType);
        foreach(array_keys($this->diversionType) as $_condition) {
            $uri=$result->$_condition;

            if ($this->absolute_voicemail_uri && $uri == "<voice-mailbox>") {
                $uri = $this->voicemail['Account'];
            }

            if (preg_match("/^(sip:|sips:)(.*)$/i",$uri,$m)) {
                $uri=$m[2];
            }

            //if (!$uri) $uri=NULL;
            $this->diversions[$condition]=$uri;
        }

        dprint_r($this->diversions);

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

    function removeVoicemailDiversions() {
        dprint ("removeVoicemailDiversions()");

        $this->getDiversions();

		$diversions=array();

        foreach (array_keys($this->diversionType) as $key) {
            if ($this->diversions[$key]=="<voice-mailbox>" || preg_match("/voicemail_server/",$this->diversions[$key])) {
                $diversions_have_changed=true;
            } else {
                if ($this->diversions[$key]) {
            		$diversions[$key]=$this->diversions[$key];
                }
            }
        }

        if (!count($diversions)) {
        	$diversions['nocondition']='empty';
        }

        if ($diversions_have_changed) {
            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->setCallDiversions($this->sipId,$diversions);
    
            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            }
        } else {
            return true;
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

    function showCreditTab() {
        $task        = $_REQUEST['task'];
        $issuer      = $_REQUEST['issuer'];
        $prepaidCard = $_REQUEST['prepaidCard'];
        $prepaidId   = $_REQUEST['prepaidId'];

        $_done       = false;

        if ($issuer) {
            print "
            <tr>
            <td colspan=2 align=left> ";
    
            if ($issuer=='subscriber'){
        		if ($prepaidCard && $prepaidId) {
                    if ($result = $this->addBalanceSubscriber($prepaidCard,$prepaidId)) {
                        print "<p><font color=green>";
                        printf (_("Old balance was %s, new balance is %s. "),$result->old_balance, $result->new_balance);
                        print "</font>";
                        $_done=true;
                    }
                }
            } else if ($issuer=='reseller' || $issuer=='admin') {
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
                $subject=sprintf ("SIP Account %s balance update",$this->account);
        
                $body="Your SIP Account balance has been updated. ".
                "For more details go to $this->sip_settings_page?tab=credit";
        
                if (mail($this->email, $subject, $body, "From: $this->support_email")) {
                    printf (_("Subscriber has been notified at %s."), $this->email);
                }
            }
    
            print "
            </td>
            </tr>
            ";
    
        }

        $this->getPrepaidStatus();

        if ($this->prepaidAccount) {
            $chapter=sprintf(_("Current Balance"));
            $this->showChapter($chapter);
    
            print "
            <tr>
            <td align=left colspan=2>";
            print _("Your current balance is");
            print ": ";
            printf ("%.2f %s ",$this->prepaidAccount->balance,$this->currency);
            print "</td><td align=right>
            </td>
            </tr>
            ";
    
            $this->showIncreaseBalanceReseller();
            $this->showIncreaseBalanceSubscriber();
            $this->showBalanceHistory();

        }
    }

    function showIncreaseBalanceReseller () {
    	if (!$this->prepaid_changes_allowed) return false;

	    $chapter=sprintf(_("Add Balance")).' ('.$this->login_type.')';
        $this->showChapter($chapter);

        print "
        <tr>
        <form action=$this->url method=post>
        <input type=hidden name=tab value=credit>
        <input type=hidden name=issuer value=reseller>
        <input type=hidden name=task value=Add>
        <td align=left colspan=2><nobr>
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
        $this->showPrepaidVoucherForm();
    }

    function showPrepaidVoucherForm () {

        if ($this->isEmbedded()) return true;

        $chapter=sprintf(_("Prepaid Card"));
        $this->showChapter($chapter);

        print "
        <tr>
        <td colspan=3>
        <p>
        ";

        printf (_("To add Credit to your account using a Prepaid Card enter it below. "));

        print "
        </td>
        </tr>
        ";

        print "
        <tr>
        <form action=$this->url method=post>
        <input type=hidden name=tab value=credit>
        <input type=hidden name=issuer value=subscriber>
        <input type=hidden name=task value=Add>
        <td align=left colspan=2><nobr>
        ";

        print _("Card Id");
        print "
        <input type=text size=10 name=prepaidId>
        ";
        print _("Card Number");
        print "
        <input type=text size=20 name=prepaidCard>
        ";

        if ($this->login_type != 'subscriber') {
            print _("Notify");
            print "<input type=checkbox name=notify value=1>
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
            unset($this->prepaidAccount);
            return false;
        }  else {
        	$this->prepaidAccount=$result[0];
        	return true;
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
        dprint("getBalanceHistory()");
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

        $this->balance_history=$result->entries;
    }

    function showBalanceHistory() {
    	$this->getBalanceHistory();
 
        if (!count($this->balance_history)) {
            return;
        }
        $chapter=sprintf(_("Balance History"));
        $this->showChapter($chapter);

        print "
        <tr>
        <td colspan=2>
        ";

        print "
        <p>
        <table width=100% cellpadding=1 cellspacing=1 border=0>";
        print "<tr>";
        print "<td class=list_header>";
        print "</td>";
        print "<td class=list_header>";
        print _("Date and Time");
        print "</td>";
        print "<td class=list_header>";
        print _("Action");
        print "</td>";
        print "<td class=list_header>";
        print _("Description");
        print "</td>";
        print "<td class=list_header align=right>";
        print _("Value");
        print "</td>";
        print "<td class=list_header align=right>";
        print _("Balance");
        print "</td>";
        print "</tr>";

        foreach ($this->balance_history as $_line) {

			if (strstr($_line->description,'Session')) {
            	if (!$_line->value) continue;
                $value=$_line->value;

                if ($this->cdrtool_address && !$this->isEmbedded()) {
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

            $rr=floor($found/2);
            $mod=$found-$rr*2;
    
            if ($mod ==0) {
                $_class='odd';
            } else {
                $_class='even';
            }

            print "
            <tr class=$_class>";

            printf ("
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
            <tr>
            <td></td>
            <td></td>
            <td></td>
            <td><b><font color=blue>%s</font></b></td>
            <td align=right><b>%s</b></td>
            <td align=right></td>
            </tr>
            ",
            _("Total Credit"),
            number_format($total_credit,4)
            );
        }

        if (strlen($total_debit)) {
        printf ("
            <tr>
            <td></td>
            <td></td>
            <td></td>
            <td><b><font color=red>%s</font></b></td>
            <td align=right><b>%s</b></td>
            <td align=right></td>
            </tr>
            ",
            _("Total Debit"),
            number_format($total_debit,4)
            );
        }

        print "
        </table>
        ";

		if ($found) {
			if (!$this->isEmbedded()) {
        		print "<p><a href=$this->url&tab=credit&action=get_balance_history&csv=1 target=_new>";
                print _("Export balance history in CSV format");
                print "</a>";
            } else {
        		print "<p><a href=$this->url&tab=credit&action=get_balance_history&csv=1>";
                print _("Export balance history in CSV format");
                print "</a>";
            }
        }
        print "</td></tr>";
    }

    function exportBalanceHistory() {
        Header("Content-type: text/csv");
    	$h=sprintf("Content-Disposition: inline; filename=%s-prepaid-history.csv",$this->account);
    	Header($h);

        print _("Id");
        print ",";
        print _("Account");
        print ",";
        print _("Date");
        print ",";
        print _("Action");
        print ",";
        print _("Description");
        print ",";
        print _("Value");
        print ",";
        print _("Final Balance");
        print ("\n");

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
        foreach (array_keys($this->diversionType) as $condition) {
            $_prefName = $condition."_lastOther";
            $this->CallPrefLastOther[$condition]= $this->Preferences[$_prefName];
        }

        if (!count($conditions)) {
            $conditions=$this->diversionType;
        }

        foreach (array_keys($conditions) as $condition) {

            $found++;
            $rr=floor($found/2);
            $mod=$found-$rr*2;

            $pref_name  = $conditions[$condition];
            $pref_value = $this->diversions[$condition];

            $select_name=$condition."_select";

            $set_uri_java="set_uri_" . $condition;
            $update_text_java="update_text_" . $condition;

            if ($mod ==0) {
                $_class='odd';
            } else {
                $_class='even';
            }

            print "
            <tr class=$_class>

              <td valign=middle>$pref_name</td>
              <td valign=middle align=left>
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

            if ($this->diversions[$condition]) {
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
                        $name .= sprintf(' (%s %s0)',_("Dial"),$this->access_numbers[$condition]);
                    } else if ($phone['description'] == "Voicemail") {
                        $name .= sprintf(' (%s %s1)',_("Dial"),$this->access_numbers[$condition]);
                    } else if ($phone['description'] == "Disabled") {
                        $name .= sprintf(' (%s %s)',_("Dial"),$this->access_numbers[$condition]);
                    } else if ($phone['description'] == "Other") {
                        $name .= sprintf(' (%s %s+ %s)',_("Dial"),$this->access_numbers[$condition],_("Number"));
                    }
                }

                if ($phone['description'] == 'Other')
                    $value = $lastOther;
                else
                    $value = $phone['value'];

                if (!$foundSelected &&
                    ($this->diversions[$condition]==$phone['value'] || $idx==$nr_targets-1)) {
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

            $pref_value=$this->diversions[$condition];

            print "
                <span>
                <td>
                  <input class=$style type=text size=40
                         name=$condition value=\"$pref_value\"
                         onChange=$update_text_java(this)>
                </span>
                ";

            if ($condition=="FUNV" && $this->FUNC_access_number) {
                printf (_("Dial %s2*X where X = Number of Minutes, 0 to Reset"), $this->access_numbers['FUNC']);
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
        <table class=settings border=0 width=100%>
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
        <table class=settings border=0 width=100%>
        ";
    }

    function chapterTableStop() {
        print "
        </table>
        ";
    }


    function getEnumMappings () {
        dprint("getEnumMappings()");

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
        <tr class=even>
          <td>";
            print _("Quick Dial");
            print "
          </td>
          <td align=left>
            <input type=text size=15 maxsize=64 name=quickdial value=\"$this->quickdial\">
            ";
            if ($this->quickdial && preg_match("/^$this->quickdial/",$this->username)) {
                $dial_suffix=strlen($this->username) - strlen($this->quickdial);
            }
            printf (_("Prefix to auto-complete short numbers"),$dial_suffix);
            print "
          </td>
        </tr>
        ";
    }


    function showMobileNumber() {

        print "
        <tr class=odd>
          <td>";
            print _("Mobile Number");
            printf ("
          </td>
          <td align=left>
            <input type=text size=15 maxsize=64 name=mobile_number value='%s'> %s
          </td>
        </tr>
        ",$this->Preferences['mobile_number'],_("International format starting with +"));
    }

    function showCallsTab() {

        $this->getCalls();

        if ($this->calls) {
            $chapter=sprintf(_("Call Statistics"));
            $this->showChapter($chapter);

            $calltime=normalizeTime($this->duration);
    
            print "
            <tr>
              <td class=cell>";
                if ($this->cdrtool_address) {
                    print "<a href=$this->cdrtool_address target=cdrtool>";
                    print _("Summary");
                    print "</a>";
                } else {
                    print _("Usage Data");
                }
                print "
              </td>
              <td class=cell>";
              printf (_("%s calls /  %s /  %s / %.2f %s"), $this->calls, $calltime,$this->trafficPrint,$this->price,$this->currency);
              print "

              </td>
            </tr>
            ";
    
            print "
            <tr>
              <td>";
                print _("First / Last Call");
                print "
              </td>
              <td>
                $this->firstCall / $this->lastCall
              </td>
            </tr>
            ";
        }

        if (count($this->calls_received)) {
            $chapter=sprintf(_("Incoming"));
            $this->showChapter($chapter);

            $j=0;
            foreach (array_keys($this->calls_received) as $call) {
                $j++;

                $uri         = $this->calls_received[$call][from];
                $duration    = normalizeTime($this->calls_received[$call][duration]);
                $dialURI     = $this->PhoneDialURL($uri) ;
                $htmlDate    = $this->colorizeDate($this->calls_received[$call][date]);
                $htmlURI     = $this->htmlURI($uri);
                $urlURI      = urlencode($this->normalizeURI($uri));

                if (!$this->calls_received[$call]['duration']) {
                    $htmlURI = "<font color=red>$htmlURI</font>";
                }

                $rr=floor($j/2);
                $mod=$j-$rr*2;
        
                if ($mod ==0) {
                    $_class='odd';
                } else {
                    $_class='even';
                }

                print "
                <tr class=$_class>
                <td>$htmlDate</td>
                <td>
                <table border=0 width=100% cellspacing=0 cellpadding=0>
                <tr>
                <td align=left width=10>$dialURI</td>
                <td align=right width=40>$duration</td>
                <td align=right width=10></td>
                <td align=left><nobr>$htmlURI</nobr></td>
                ";
                print "<td align=right><a href=$this->url&tab=contacts&task=add&uri=$urlURI&search_text=$urlURI>$this->phonebook_img</a></td>";
                print "
                </tr>
                </table>";
                print "</td>
                </tr>
                ";
            }
        }

        if (count($this->calls_placed)) {
            $chapter=sprintf(_("Outgoing"));
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
                $urlURI      = urlencode($this->normalizeURI($uri));

                if ($price) {
                    $price_print =sprintf(" (%s %s)",$price,$this->currency);
                } else {
                    $price_print = '';
                }

                $rr=floor($j/2);
                $mod=$j-$rr*2;
        
                if ($mod ==0) {
                    $_class='odd';
                } else {
                    $_class='even';
                }

                print "
                <tr class=$_class>
                <td>$htmlDate</td>
                <td>
                <table border=0 width=100% cellspacing=0 cellpadding=0>
                <tr>
                <td align=left width=10>$dialURI</td>
                <td align=right width=40>$duration</td>
                <td align=right width=10></td>
                <td align=left><nobr>$htmlURI $price_print</nobr></td>
                ";

                print "<td align=right><a href=$this->url&tab=contacts&task=add&uri=$urlURI&search_text=$urlURI>$this->phonebook_img</a></td>";
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

        $this->call_history=array('placed'=>$this->calls_placed,
                                  'received'=>$this->calls_received
                                  );

    }

    function getCallStatistics () {
        dprint("getCallStatistics()");

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
        $name      = trim($_REQUEST['name']);

        if (!strlen($uri)) return false;

        $phonebookEntry=array('uri'       => $uri,
                              'name'      => $name
                              );

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
            $match=array('uri'  => '%'.$search_text.'%',
                         'name' => '%'.$search_text.'%'
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

    function showContactsTab() {
        dprint("showContactsTab()");

        if ($this->show_directory) {
        	$chapter=sprintf(_("Directory"));
            $this->showChapter($chapter);

            print "
            <tr>
            <td colspan=2 align=left>";
            print _("To find other SIP Addresses fill in the First Name or the Last Name and click the Search button. ");
            print "
            </td>
            </tr>
            ";

            print "
            <tr>
            <td colspan=3>";

			$this->showSearchDirectory();

            print "
            </td>
            </tr>
            ";

        }

        if ($this->rows || $_REQUEST['task']=='directory') {
            // hide local contacts if we found a global contact
            return true;
        }

        $chapter=sprintf(_("Don't Disturb")).' '.sprintf(_("Groups"));
        $this->showChapter($chapter);

        print "
        <tr>
        <td colspan=2 align=left>";
        print _("You can organize contacts into groups that can be used to accept incoming calls in Don't Disturb section. ");
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
    
        $url_string=$this->url."&tab=contacts";

        print "
        <p>
        <table width=100% cellpadding=1 cellspacing=1 border=0>
        <tr>
        <form action=$this->url method=post>
        <input type=hidden name=tab value=contacts>
        <input type=hidden name=task value=add>
        <td align=left valign=top>
        <input type=submit value=";
        print _("Add");
        print ">
        <input type=text size=20 name=uri>
        ";
        print _("(wildcard %)");
        print "
        </td>
        </form>
        ";
        if (count($this->PhonebookEntries)){
            print "
            <form action=$this->url method=post>
            <input type=hidden name=tab value=contacts>
            <td align=right valign=top>
            ";
            print _("Name");
            print "
            <input type=text size=20 name='search_text' value=\"$search_text\">
            ";
    
            $selected[$group]="selected";
    
            print "<select name=group>";
            print "<option value=\"\">";
            print _('Group');
            foreach(array_keys($this->PhonebookGroups) as $key) {
                printf ("<option value=\"%s\" %s>%s",$key,$selected[$key],$this->PhonebookGroups[$key]);
            }
            print "<option value=\"\">------";
            printf ("<option value=\"empty\" %s>%s",$selected['empty'],_("No group"));
    
            print "</select>";
    
            print "<input type=submit value=";
            print _("Search");
            print ">";
        }

        print "</td>
        </form>
        </tr>
        </table>
        ";

        if (count($this->PhonebookEntries)){
            print "
            <p>
            <table width=100% cellpadding=1 cellspacing=1 border=0>
            <tr>
            <td class=list_header align=right></td>
            ";
            print "<td class=list_header>";
            print _("SIP Address");
            print "</td>";
            print "<td class=list_header>";
            print "</td>";
            print "<td class=list_header>";
            print _("Display Name");
            print "</td>";
            print "<td class=list_header>";
            print _("Group");
            print "</td>";
            print "<td class=list_header>";
            print _("Action");
            print "</td>";
            print "</tr>";
    
            foreach(array_keys($this->PhonebookEntries) as $_entry) {
        
                $found=$i+1;

                $rr=floor($found/2);
                $mod=$found-$rr*2;
        
                if ($mod ==0) {
                    $_class='odd';
                } else {
                    $_class='even';
                }

                print "
                <tr class=$_class valign=top>
                <form name=\"Entry$found\" action=\"$this->url&tab=$this->tab\">
                $this->hiddenElements
                <input type=hidden name=tab value=\"$this->tab\">
                <input type=hidden name=task value=\"update\">
                ";
                printf ("<input type=hidden name=uri value=\"%s\">",$this->PhonebookEntries[$_entry]->uri);

                print "<td valign=top>$found</td>
                <td>";
                print $this->PhonebookEntries[$_entry]->uri;

                if (preg_match("/\%/",$this->PhonebookEntries[$_entry]->uri)) {
                   printf ("</td>
                   <td valign=top></td>
                   <td valign=top>");
                } else {
                   printf ("</td>
                   <td valign=top>%s</td>
                   <td valign=top>",
                   $this->PhoneDialURL($this->PhonebookEntries[$_entry]->uri));
                }

                if ($this->SOAPversion > 1) {
                    printf ("<input type=text name=name value='%s'>",$this->PhonebookEntries[$_entry]->name);
                    printf ("<a href=\"javascript: document.Entry$found.submit()\">%s</a>",_("Update"));
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
                    printf ("<a href=%s&task=deleteContact&uri=%s&confirm=1&search_text=%s>",$url_string,urlencode($this->PhonebookEntries[$_entry]->uri),urlencode($search_text));
                    print _("Confirm");
                } else {
                    print "
                    <td valign=top>";
                    printf ("<a href=%s&task=deleteContact&uri=%s&search_text=%s>",$url_string,urlencode($this->PhonebookEntries[$_entry]->uri),urlencode($search_text));
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
            $this->export_filename="tbook.csv";
            $phonebook.=sprintf("Name,Address,Group\n");
        } else if ($userAgent == 'eyebeam') {
            $phonebook.=sprintf("Name,Group Name,SIP URL,Proxy ID\n");
        } else if ($userAgent == 'csco') {
            $this->contentType="Content-type: text/xml";
            $this->export_filename="directory.xml";
            $phonebook.=sprintf ("<CiscoIPPhoneDirectory>\n\t<Title>%s</Title>\n\t<Prompt>Directory</Prompt>\n",$this->account);
        } else if ($userAgent == 'unidata') {
            $this->export_filename="phonebook.csv";
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
        $_header=sprintf("Content-Disposition: inline; filename=%s",$this->export_filename);
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
        //dprint_r($this->rejectMembers);

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

    function showAcceptTab() {
        $chapter=sprintf(_("Do Not Disturb"));
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
        <td colspan=2 align=left>";
        print _("You can reject calls depending on the time of day and Caller-ID. ");
        print _("You can create custom groups in the Contacts page like Family or Coworkers. ");
        print  "<p>";
        print _("Rejected calls are diverted based on the Unavailable condition in the Call Forwarding page. ");
        print "<p>";
        print "<p class=desc>";
        printf (_("Your current time is: %s"),$this->timezone);
        $timestamp=time();
        $LocalTime=getLocalTime($this->timezone,$timestamp);
        print " $LocalTime";

        print "
        </td>
        </tr>
        ";

        $chapter=sprintf(_("Temporary Rule"));
        $this->showChapter($chapter);

        print "
        <tr>
        <td colspan=2 align=left>";
        print _("This will override the permanent rules for the chosen duration. ");
        print "
        </td>
        </tr>
        ";

        print "<tr>";
        print "<td colspan=2>";

        print "<table border=0 width=100%>
            <tr>
            <td>
            ";
        print _("Duration");
        print ":";

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
            $class_nobody="checked_groups";
        } else {
            $class_nobody="note";
        }

        printf ("<td class=note><input type=radio name=%s value=0 %s>%s",$_name,$_checked_everybody,_("Everybody"));
        printf ("<td class=$class_nobody><input type=radio name=%s value=1 %s>%s",$_name,$_checked_nobody,_("Nobody"));

        $c=count($this->acceptRules['groups']);

        if ($_checked_groups) {
            $class_groups="checked_groups";
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

        $chapter=sprintf(_("Permanent Rules"));
        $this->showChapter($chapter);

        print "
        <tr valign=top>
        <td colspan=2 align=left valign=top>
        ";

        print "<table border=0 width=100%>";
        print "<tr bgcolor=lightgrey>
        <td>";
        print _("Days");
        print "</td>
        <td colspan=2>";
        print _("Time Interval");
        print "</td>
        <td colspan=3>";
        print _("Groups");
        print "</td>
        </tr>
        ";

        foreach (array_keys($this->acceptDailyProfiles) as $profile) {

            if ($this->acceptRules['persistent'][$profile]['start'] || $this->acceptRules['persistent'][$profile]['stop']) {
                $class="checked_groups";
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
                $class_nobody="checked_groups";
            } else {
                $class_nobody="note";
            }

            printf ("<td class=note><input type=radio name=%s value=0 %s>%s",$_name,$_checked_everybody,_("Everybody"));
            printf ("<td class=$class_nobody><input type=radio name=%s value=1 %s>%s",$_name,$_checked_nobody,_("Nobody"));

            $c=count($this->acceptRules['groups']);

            if ($_checked_groups) {
                $class_groups="checked_groups";
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

        $chapter=sprintf(_("Rejected Callers"));
        $this->showChapter($chapter);

        print "
        <form method=post name=reject_form onSubmit=\"return checkForm(this)\">
        ";
        print "
        <tr>
        <td colspan=2 align=left>";
        print _("Use %Number@% to match PSTN numbers and user@domain to match SIP Addresses");
        print "
        </td>
        </tr>
        ";

        print "<tr>";
        print "<td align=left>";
        print _("SIP Address");
        print "</td>";
        print "<td align=left>
        <input type=text size=35 name=rejectMembers[]>
        </td>";
        print "<tr>";

        if ($this->getRejectMembers()) {
            foreach ($this->rejectMembers as $_member) {
                $j++;

                $rr=floor($j/2);
                $mod=$j-$rr*2;
        
                if ($mod ==0) {
                    $_class='odd';
                } else {
                    $_class='even';
                }

                print "
                <tr class=$_class>";

                print "<td align=left>";
                print _("SIP Address");
                print "</td>";
                print "<td align=left>
                <input type=text size=35 name=rejectMembers[] value=\"$_member\">
                </td>";
                print "<tr>";
            }
        }

        print "
        <tr>
          <td align=left>
            <input type=hidden name=action value=\"set reject\">
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
        $this->getEnumMappings();
        $this->getAliases();

        $this->countAliases=count($this->aliases);

        if (!$this->email && !$skip_html) {
            print "<p><font color=blue>";
            print _("Please fill in the e-mail address. ");
            print "</font>";
            return false;
        }

        $subject = sprintf("SIP Account settings %s",$this->account);

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

        foreach ($this->form_elements as $el) {
            ${$el}=trim($_REQUEST[$el]);
        }

        if ($accept_temporary_remain && !is_numeric($accept_temporary_remain)) {
            $this->error=_("Invalid Expiration Period");
            return false;
        }

        if ($quota && !is_numeric($quota) && !is_float($quota)) {
            $this->error=_("Invalid Quota");
            return false;
        }

        if (!$timezone && !$this->timezone) {
            $this->error=_("Missing Timezone");
            return false;
        }

        if (!$this->checkEmail($mailto)) {
            $this->error=_("Invalid E-mail Address");
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
            srand((double)microtime(true)*1000000);
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
    
    function showUpgradeTab () {
    }

    function PhoneDialURL($uri) {
        $uri=$this->normalizeURI($uri);
        if (!preg_match("/^sip:/",$uri)) {
            $uri="sip:".$uri;
        }
        $uri_print="<a href=$uri>$this->call_img</a>";
        return $uri_print;
    }

    function showChapter($chapter) {
        print "
        <tr>
          <td class=chapter colspan=2><b>";
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

    function showPresenceTab() {

        if (!$this->password) {
            print "Error: the acocunt password is not available in clear text. You cannot retrieve XCAP documents from this page.";
            return false;
        }

        $this->getPresenceWatchers();
        $this->getPresenceRules();
        $this->getPresenceInformation();

        print "
        <form method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";
        $chapter=sprintf(_("Activity"));
        $this->showChapter($chapter);

        print ("
        <tr>
        <td>");
        print _("Note");
        print "</td>
        <td>";
        print _("Activity");
        print "</td>
        </tr>";

        printf ("
        <tr>
          <td><input type=text size=50 name=note value='%s'>
          <td>
          <select name=activity>
        <option>
        ",$this->presentity['note']);

        $selected_activity[$this->presentity['activity']]='selected';

        foreach (array_keys($this->presence_activities) as $_activity) {
            printf ("<option %s value='%s'>%s",$selected_activity[$_activity],$_activity,ucfirst($_activity));
        }
        print "</select>";

        if ($this->presence_activities[$this->presentity['activity']]) {
            printf ("<img src=images/%s border=0>",$this->presence_activities[$this->presentity['activity']]);
        }

        print "
          </td>
        </tr>
        ";

        $chapter=sprintf(_("Watchers"));
        $this->showChapter($chapter);

        $j=0;

        foreach (array_keys($this->presence_watchers) as $_watcher) {
            $j++;

            $online_icon='';

            if (is_array($this->presence_rules['allow']) && in_array($_watcher,$this->presence_rules['allow'])) {
                $display_status = 'allow';
            } elseif (is_array($this->presence_rules['deny']) && in_array($_watcher,$this->presence_rules['deny'])) {
                $display_status = 'deny';
            } else {
                $display_status = $this->presence_watchers[$_watcher]['status'];
            }

            if ($this->presence_watchers[$_watcher]['online'] == 1) {
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
            <td><font color=%s>%s</font></td>
            <td>
            <select name=watcher_status[]>
            ",
            $_watcher,
            $color,
            $_watcher);

            unset($selected);
            $selected[$display_status]='selected';

            foreach ($this->presence_statuses as $_status) {
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
        foreach (array_keys($this->presence_rules) as $_key) {
            $j++;

            foreach ($this->presence_rules[$_key] as $_tmp) {

                if (in_array($_tmp,array_keys($this->presence_watchers))) {
                    continue;
                }

                printf ("
                <tr>
                <input type=hidden name=watcher[] value=%s>
                <td>%s</td>
                <td>
                <select name=watcher_status[]>
                ",$_tmp,$_tmp);

                unset($selected);
                $selected[$_key]='selected';

                foreach ($this->presence_statuses as $_status) {
                    if ($_status== 'confirm' && !$selected[$_status]) continue;
                    printf ("<option %s value=%s>%s",$selected[$_status],$_status,ucfirst($_status));
                }
                print "
                <option value=delete>";
                print _("Delete");
                print "
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
        <td><input type=text name=watcher[]></td>
        <td>
        <select name=watcher_status[]>
        ");
        $selected['deny']='selected';
        foreach ($this->presence_statuses as $_status) {
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
        foreach ($this->presence_statuses as $_status) {
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

        if (!$this->password) {
            print "Error: password is not available in clear text";
            return false;
        }

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
            $this->presence_watchers[$_watcher->id]['status']=$_watcher->status;
            $this->presence_watchers[$_watcher->id]['online']=$_watcher->online;
            $this->watchersOnline=0;
            if ($this->presence_watchers[$_watcher->id]['online']) {
                $this->watchersOnline++;
            }
        }
        //dprint_r($this->presence_watchers);

    }

    function getPresenceInformation() {
        dprint("getPresenceInformation()");

        if (!$this->password) {
            print "<p>Error: password is not available in clear text";
            return false;
        }

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

        if (!$this->password) {
            print "<p>Error: password is not available in clear text";
            return false;
        }

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

        if (!$this->password) {
            print "<p>Error: password is not available in clear text";
            return false;
        }

        $result = $this->PresencePort->getPolicy(array("username" =>$this->username,"domain"   =>$this->domain),$this->password);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (PresencePort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        foreach ($this->presence_statuses as $_status) {
            $this->presence_rules[$_status] = $result->$_status;
        }

        //dprint_r($this->presence_rules);
    }

    function getFileTemplate($name, $type="file") {
    
        dprint("getFileTemplate(name=$name, type=$type, path=$this->templates_path)");

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
        dprint("getBillingProfiles()");
        // Get getBillingProfiles
        if ($this->SOAPversion < 2) return true;

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

        $chapter=sprintf(_("Billing Profiles"));
        $this->showChapter($chapter);

        print "
        <tr class=even>
          <td valign=top>";
            print _("Weekdays");
            printf ("
          </td>
          <td align=left>
            <input type=text size=10 maxsize=64 name=profileWeekday value='%s'>/
            <input type=text size=10 maxsize=64 name=profileWeekdayAlt value='%s'>
            ",
            $this->billingProfiles->profileWeekday,
            $this->billingProfiles->profileWeekdayAlt
            );

            print "
          </td>
        </tr>
        ";

        print "
        <tr class=odd>
          <td valign=top>";
            print _("Weekends");
            printf ("
          </td>
          <td align=left>
            <input type=text size=10 maxsize=64 name=profileWeekend value='%s'>/
            <input type=text size=10 maxsize=64 name=profileWeekendAlt value='%s'>
            ",
            $this->billingProfiles->profileWeekend,
            $this->billingProfiles->profileWeekendAlt
            );

            print "
          </td>
        </tr>
        ";

        print "
        <tr class=even>
          <td valign=top>";
            print _("Timezone");
            printf ("
          </td>
          <td align=left>
            ");

            if ($this->billingProfiles->timezone) {
                $_timezone=$this->billingProfiles->timezone;
            } else {
                $_timezone=$this->resellerProperties['timezone'];
            }

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

        if ($this->login_type == 'subscriber') {
            printf ("<input type=hidden name=extra_groups value='%s'>",trim($extraGroups_text));
        } else {
            print "
            <tr>
            <td>";
            print _("Extra Groups");
            print "
            </td>
            <td>";
            printf ("<input type=text size=30 name=extra_groups value='%s'>",trim($extraGroups_text));
            print "
            </td>
            </tr>
            ";
        }
    }

    function generateCertificate() {

		global $enrollment;
		include("/etc/cdrtool/enrollment/config.ini");

        if (!is_array($enrollment)) {
            print _("Error: missing enrollment settings");
            return false;
        }

        if (!$enrollment['ca_conf']) {
            //print _("Error: missing enrollment ca_conf settings");
            return false;
        }

        if (!$enrollment['ca_crt']) {
            //print _("Error: missing enrollment ca_crt settings");
            return false;
        }

        if (!$enrollment['ca_key']) {
            //print _("Error: missing enrollment ca_key settings");
            return false;
        }

    	$config = array(
    		'config'           => $enrollment['ca_conf'],
    		'digest_alg'       => 'md5',
    		'private_key_bits' => 1024,
    		'private_key_type' => OPENSSL_KEYTYPE_RSA,
    		'encrypt_key'      => false,
    	);

		$dn = array(
    		"countryName"            => "NL",
	    	"stateOrProvinceName"    => "Noord Holland",
    		"localityName"           => "Haarlem",
    		"organizationName"       => "AG Projects",
    		"organizationalUnitName" => "Blink",
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

		$ca="file://".$enrollment['ca_crt'];

		$this->crt = openssl_csr_sign($this->csr, $ca, $enrollment['ca_key'], 3650, $config);

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
                     'ca'  => file_get_contents($enrollment['ca_crt'])
                     );
        return $ret;
    }

    function exportCertificateX509() {
        Header("Content-type: application/x-crt");
        $header=sprintf("Content-Disposition: inline; filename=%s.crt",$this->account);
		Header($header);
        $cert=$this->generateCertificate();
        $crt=$cert['crt'].$cert['key'];
        print $crt;
    }

    function exportCertificateP12() {
        $cert=$this->generateCertificate();
        Header("Content-type: application/x-p12");
        $header=sprintf("Content-Disposition: inline; filename=%s.p12",$this->account);
		Header($header);
        print $cert['p12'];
    }

    function isEmbedded() {
        // return true if page was loaded from non-session based web session
		if ($_SERVER['SSL_CLIENT_CERT'] || $_SERVER['PHP_AUTH_DIGEST']) {
            return true;
        }
        return false;
    }

    function changeLanguage($lang='en',$domain='cdrtool') {
        // run dpkg-reconfigure locales and select support languages .utf8
    
        $lang = $this->languageCodeFor(isset($lang) ? $lang : 'en');
        $lang.='.utf8';
        setlocale(LC_ALL, $lang); 
        bindtextdomain($domain, '/var/www/CDRTool/po/locale');
        bind_textdomain_codeset($domain,'UTF-8');
        textdomain($domain);
    }
    
    // return full language code for given 2 letter language code
    function languageCodeFor($lang='en') {
        $lang = isset($lang) ? strtolower($lang) : 'en';
        switch ($lang) {
            case 'en': return 'en_US'; // this can be C or en_US
            case 'ja': return 'ja_JP';
            default  : return ($lang . '_' . strtoupper($lang));
        }
        return 'C'; // this will never be reached
	}

    function showDirectorySearchForm () {
        print "
        <p>
        <table width=100% cellpadding=1 cellspacing=1 border=0>
        <tr>
        <form action=$this->url method=post>
        <input type=hidden name=tab value='contacts'>
        ";
        print $this->hiddenElements;
        print "
        <td align=left valign=top colspan=3>";
        print _("First Name");
        printf (" <input type=text size=20 name='firstname' value='%s'> ",$_REQUEST['firstname']);

        print _("Last Name");

        printf (" <input type=text size=20 name='lastname' value='%s'>",$_REQUEST['lastname']);

        print "</td>
        </tr>
        <tr>
        <td colpsan=3 valign=top>";
        print "<br><input type=submit value=";
        print _("Search");
        print ">";
        print "</td>
        </form>
        </tr>
        </table>
        ";
    }

    function showSearchDirectory() {

        if (!$this->show_directory) {
            return false;
        }

		$this->maxrowsperpage=20;

        $this->showDirectorySearchForm();

        if ($_REQUEST['firstname'] || $_REQUEST['lastname']) {
        	if ($_REQUEST['firstname'] && strlen($_REQUEST['firstname']) < 3) {
                return false;
            }
        	if ($_REQUEST['lastname'] && strlen($_REQUEST['lastname']) < 3) {
                return false;
            }

        } else {
            return false;
        }

        $this->next       = $_REQUEST['next'];

        // Filter
        $filter=array('firstName'=> trim($_REQUEST['firstname']),
                      'lastName' => trim($_REQUEST['lastname'])
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
        $this->SipPort->addHeader($this->SoapAuthAdmin);

        // Call function
        $result     = $this->SipPort->getAccounts($Query);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        $this->rows = $result->total;

        if (!$this->next)  $this->next=0;

        if ($this->rows > $this->maxrowsperpage)  {
            $maxrows = $this->maxrowsperpage + $this->next;
            if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
        } else {
            $maxrows=$this->rows;
        }

        if ($this->rows) {
            print "
            <table border=0 align=center>
            <tr><td>";

            printf(_("%s contacts found. "),$this->rows);

            if ($this->isEmbedded()) {
            	printf (_("Click on %s to add a Contact to Blink. "),$this->plus_sign_img);
            }
            print "</td></tr>
            </table>
            <p>
            <table border=0 cellpadding=2 width=100%>
            <tr bgcolor=lightgrey>
            <td bgcolor=white></td>";
    
            print "<td><b>";
            print _('Display Name');
            print "</b></td>";
            print "<td><b>";
            print _('SIP Address');
            print "</b></td>";
            print "<td><b>";
            print _('Timezone');
            print "</b></td>";
            print "<td><b>";
            print _('Action');
            print "</b></td>";
            print "
            </tr>
            ";

            $i=0;

            while ($i < $maxrows)  {

                if (!$result->accounts[$i]) break;

                $account = $result->accounts[$i];

                $index=$this->next+$i+1;

                $rr=floor($index/2);
                $mod=$index-$rr*2;
        
                if ($mod ==0) {
                    $_class='odd';
                } else {
                    $_class='even';
                }
    
                $i++;
                $name=$account->firstName.' '.$account->lastName;
	            $sip_account=sprintf("%s@%s",$account->id->username,$account->id->domain);
                $contacts_url=sprintf("<a href=%s&tab=contacts&task=add&uri=%s&name=%s&search_text=%s>%s</a>",$this->url,$sip_account,urlencode($name),$sip_account,$this->phonebook_img);

                if ($this->isEmbedded()) {

                    $add_contact_url=sprintf("<a href=\"javascript:blink.addContact_withDisplayName_('%s', '%s');\">%s</a>",$sip_account,$name,$this->plus_sign_img);
                    printf ("<tr class=%s><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s %s</td>",
                    $_class,
                    $index,
                    $name,
                    $sip_account,
                    $account->timezone,
                    $this->PhoneDialURL($sip_account),
                    $add_contact_url
                    );
                } else {

                    printf ("<tr class=%s><td>%d</td><td>%s</td><td>%s</td><td>%s</td><td>%s %s</td>",
                    $_class,
                    $index,
                    $name,
                    $sip_account,
                    $account->timezone,
                    $this->PhoneDialURL($sip_account),
                    $contacts_url
                    );
                }
            }

            print "</table>";
    
            $this->showPagination($maxrows);
    
            return true;
    	}
    }

    function showPagination($maxrows) {

        $url = sprintf("%s&tab=%s&firstname=%s&lastname%s",
               $this->url,
               $this->tab,
               urlencode($_REQUEST['firstname']),
               urlencode($_REQUEST['lastname'])
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

    function setMSN() {
        $result      = $this->result;
        $this->properties=$result->properties;

        $_msn_credentials=trim($_REQUEST['msn_address']).":".trim($_REQUEST['msn_password']);
        $this->setPreference('msn_credentials',$_msn_credentials);

      	$result->properties=$this->properties;

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->updateAccount($result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $this->Preferences['msn_credentials']=$_msn_credentials;
            return true;
        }

    }

    function setYahoo() {
        $result      = $this->result;
        $this->properties=$result->properties;

        $_yahoo_credentials=trim($_REQUEST['yahoo_address']).":".trim($_REQUEST['yahoo_password']);

        $this->setPreference('yahoo_credentials',$_yahoo_credentials);
      	$result->properties=$this->properties;

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->updateAccount($result);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $this->Preferences['yahoo_credentials']=$_yahoo_credentials;
            return true;
        }
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

function getSipAccountFromX509Certificate() {

     if (!$_SERVER[SSL_CLIENT_CERT]) {
     	print _("Error: No X.509 client certificate provided\n");
        return false;
     }

     if (!$cert=openssl_x509_parse($_SERVER[SSL_CLIENT_CERT])) {
     	print _("Error: Failed to parse X.509 client certificate\n");
        return false;
     }

     $username    = $cert['subject']['CN'];

     $a=explode("@",$username);
     $domain= $a[1];

     if (count($a) !=2 ) {
         print _("No SIP Address available. ");
         return false;
     }

     require("/etc/cdrtool/ngnpro_engines.inc");

     global $domainFilters, $resellerFilters, $soapEngines ;

     $credentials['account']    = $username;

     if ($domainFilters[$domain]['sip_engine']) {
         $credentials['engine']   = $domainFilters[$domain]['sip_engine'];
         $credentials['customer'] = $domainFilters[$domain]['customer'];
         $credentials['reseller'] = $domainFilters[$domain]['reseller'];

     } else if ($domainFilters['default']['sip_engine']) {
         $credentials['engine']=$domainFilters['default']['sip_engine'];
     } else {
         print "Error: no domainFilter available in ngnpro_engines.inc";
         return false;
     }

     $SOAPlogin=array(
                            "username" => $soapEngines[$credentials['engine']]['username'],
                            "password" => $soapEngines[$credentials['engine']]['password'],
                            "admin"    => true
     );

     $SoapAuth = array('auth', $SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

     $SipPort  = new WebService_NGNPro_SipPort($soapEngines[$credentials['engine']]['url']);

     $SipPort->_options['timeout'] = 5;
     $SipPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
     $SipPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
     $SipPort->addHeader($SoapAuth);

     $result     = $SipPort->getAccount(array("username" =>$a[0],"domain"   =>$domain));

     if (PEAR::isError($result)) {
         $error_msg  = $result->getMessage();
         $error_fault= $result->getFault();
         $error_code = $result->getCode();
         printf ("<p><font color=red>Error from %s (SipPort): %s (%s): %s</font>",$soapEngines[$credentials['engine']]['url'],$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
         return false;
     }

     $credentials['customer'] = $result->customer;
     $credentials['reseller'] = $result->reseller;

     return $credentials;
}

function getSipAccountFromHTTPDigest () {

    require("/etc/cdrtool/enrollment/config.ini");
 
    if (!is_array($enrollment) || !strlen($enrollment['nonce_key'])) {
        syslog(LOG_NOTICE, 'Missing nonce in enrollment settings');
        die('Missing enrollment settings');
        return false;
    }

    if ($_REQUEST['realm']) {
        // required by Blink cocoa
        $realm=$_REQUEST['realm'];
    } else {
    	$realm = 'SIP_settings';
    }

    // security implemented based on
    // http://static.springsource.org/spring-security/site/docs/2.0.x/reference/digest.html

    $_id   = microtime(true)+ 300;  // expires 5 minutes in the future
    $_key  = $enrollment['nonce_key'];
    $nonce = base64_encode($_id.":".md5($_id.":".$_key));

    if (empty($_SERVER['PHP_AUTH_DIGEST'])) {
        header('HTTP/1.1 401 Unauthorized');

        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');
    
        die('You have canceled login');
    }
    
    // analyze the PHP_AUTH_DIGEST variable
    if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
        !isset($data['username']))
        die('Wrong Credentials!');
    

    // generate the valid response
    $username    = $data['username'];

    $a=explode("@",$username);
    $domain= $a[1];

    if (count($a) !=2 ) {
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');
    
        die("Invalid username, must be in the format user@domain");
    }

    if (!strlen($domain)) {
    	header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');
    
        die("Invalid domain name");
    }

    require("/etc/cdrtool/ngnpro_engines.inc");

    global $domainFilters, $resellerFilters, $soapEngines ;

    $credentials['account']    = $username;

    if ($domainFilters[$domain]['sip_engine']) {
        $credentials['engine']   = $domainFilters[$domain]['sip_engine'];
        $credentials['customer'] = $domainFilters[$domain]['customer'];
        $credentials['reseller'] = $domainFilters[$domain]['reseller'];

    } else if ($domainFilters['default']['sip_engine']) {
        $credentials['engine']=$domainFilters['default']['sip_engine'];
    } else {
        die("Error: no domainFilter available in ngnpro_engines.inc");
    }

    $SOAPlogin=array(
                           "username" => $soapEngines[$credentials['engine']]['username'],
                           "password" => $soapEngines[$credentials['engine']]['password'],
                           "admin"    => true
    );

    $SoapAuth = array('auth', $SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

    $SipPort  = new WebService_NGNPro_SipPort($soapEngines[$credentials['engine']]['url']);

    $SipPort->_options['timeout'] = 5;
    $SipPort->setOpt('curl', CURLOPT_SSL_VERIFYPEER, 0);
    $SipPort->setOpt('curl', CURLOPT_SSL_VERIFYHOST, 0);
    $SipPort->addHeader($SoapAuth);

    $result = $SipPort->getAccount(array("username" =>$a[0],"domain"   =>$domain));

    if (PEAR::isError($result)) {
        $error_msg  = $result->getMessage();
        $error_fault= $result->getFault();
        $error_code = $result->getCode();
    	header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');
        die('Wrong Credentials!');
    }

    $A1 = md5($data['username'] . ':' . $realm . ':' . $result->password);
    $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
    $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);
    
    if ($data['response'] != $valid_response) {
    	header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');

        die('Wrong Credentials!');
    }
    // check nonce

	$client_nonce_els=explode(":",base64_decode($data['nonce']));

	if (md5($client_nonce_els[0].":".$_key) != $client_nonce_els[1]) {
    	header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');

        die('Wrong nonce!');
    }


	if (microtime(true) > $client_nonce_els[0]) {
        // nonce is stale
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",stale=true,opaque="'.md5($realm).'"');

        die('Nonce has expired!');
    }

    $credentials['customer'] = $result->customer;
    $credentials['reseller'] = $result->reseller;

    return $credentials;
}

function http_digest_parse($txt) {
    // function to parse the http auth header
    // protect against missing data
    $needed_parts = array('nonce'=>1, 'nc'=>1, 'cnonce'=>1, 'qop'=>1, 'username'=>1, 'uri'=>1, 'response'=>1);
    $data = array();
    $keys = implode('|', array_keys($needed_parts));

    preg_match_all('@(' . $keys . ')=(?:([\'"])([^\2]+?)\2|([^\s,]+))@', $txt, $matches, PREG_SET_ORDER);

    foreach ($matches as $m) {
        $data[$m[1]] = $m[3] ? $m[3] : $m[4];
        unset($needed_parts[$m[1]]);
    }

    return $needed_parts ? false : $data;
}

function renderUI($SipSettings_class,$account,$login_credentials,$soapEngines) {
    // Generic code for all sip settings pages

	$SipSettings = new $SipSettings_class($account,$login_credentials,$soapEngines);

    if (!strstr($_REQUEST['action'],'get_') &&
        !strstr($_REQUEST['action'],'set_') &&
        !strstr($_REQUEST['action'],'export_') &&
        !strstr($_REQUEST['action'],'add_')) {
        $title  = "SIP settings of $account";
        $header = $SipSettings->headerFile;
        $css    = $SipSettings->cssFile;

		$auto_refesh_tab=$SipSettings->auto_refesh_tab;
        $absolute_url= $SipSettings->absolute_url;

        include($header);
        dprint("Header file $header included, refresh=$auto_refesh_tab");
        include($css);
        dprint("CSS file $css included");

    }

    if ($_REQUEST['action']=="save settings") {
        if ($SipSettings->checkSettings()) {
            $SipSettings->saveSettings();
            unset($SipSettings);
            $SipSettings = new $SipSettings_class($account,$login_credentials,$soapEngines);

        } else {
            print "<font color=red>";
            printf (_("Error: %s"),$SipSettings->error);
            print "</font>";
        }
    } else if ($_REQUEST['action']=="set diversions") {
        $SipSettings->setDiversions();
        unset($SipSettings);
        $SipSettings = new $SipSettings_class($account,$login_credentials,$soapEngines);
    } else if ($_REQUEST['action']=="set barring") {
        $SipSettings->setBarringPrefixes();
    } else if ($_REQUEST['action']=="set presence") {
        $SipSettings->setPresence();
    } else if ($_REQUEST['action']=="set reject") {
        $SipSettings->setRejectMembers();
    } else if ($_REQUEST['action']=="set accept rules") {
        $SipSettings->setAcceptRules();
    } else if ($_REQUEST['action']=="set aliases") {
        $SipSettings->setAliases();
    } else if ($_REQUEST['action']=="set msn") {
        $SipSettings->setMSN();
    } else if ($_REQUEST['action']=="set yahoo") {
        $SipSettings->setYahoo();
    } else if ($_REQUEST['action']=="send email") {
        $SipSettings->sendEmail();
    } else if ($_REQUEST['action']=="get_crt") {
        $SipSettings->exportCertificateX509();
        return true;
    } else if ($_REQUEST['action']=="get_p12") {
        $SipSettings->exportCertificateP12();
        return true;
    } else if ($_REQUEST['action'] == 'get_balance_history') {
        $SipSettings->getBalanceHistory();
    	if ($_REQUEST['csv']) {
        	$SipSettings->exportBalanceHistory();
        } else {
           print json_encode($SipSettings->balance_history);
        }
        return true;
    } else if ($_REQUEST['action'] == 'get_call_forwarding') {
        $SipSettings->getDiversions();
        print json_encode($SipSettings->diversions);
        return true;
    } else if ($_REQUEST['action'] == 'get_prepaid') {
        $SipSettings->getPrepaidStatus();
        print json_encode($SipSettings->prepaidAccount);
        return true;
    } else if ($_REQUEST['action'] == 'get_monthly_usage') {
        $SipSettings->getCallStatistics();
        print json_encode($SipSettings->thisMonth);
        return true;
    } else if ($_REQUEST['action'] == 'get_accept_rules'){
        $SipSettings->getAcceptRules();
        print json_encode($SipSettings->acceptRules);
        return true;
    } else if ($_REQUEST['action'] == 'get_reject_rules'){
        $SipSettings->getRejectMembers();
        print json_encode($SipSettings->rejectMembers);
        return true;
    } else if ($_REQUEST['action'] == 'get_calls'){
        $SipSettings->getCalls();
        print json_encode($SipSettings->call_history);
        return true;
    } else if ($_REQUEST['action'] == 'get_voicemail'){
        $SipSettings->getVoicemail();
        print json_encode($SipSettings->voicemail);
        return true;
    } else if ($_REQUEST['action'] == 'get_aliases'){
        $SipSettings->getAliases();
        print json_encode($SipSettings->aliases);
        return true;
    } else if ($_REQUEST['action'] == 'get_enum'){
        $SipSettings->getEnumMappings();
        print json_encode($SipSettings->enums);
        return true;
    } else if ($_REQUEST['action'] == 'export_identity_proof'){
        $SipSettings->exportIdentityProof();
        return true;
    } else if ($_REQUEST['action'] == 'add_balance'){

        if (!$_REQUEST['id'] || !$_REQUEST['number']) {
            $return=array('success'       => false,
                          'error_message' => 'Missing id or number'
                          );
            print (json_encode($return));
            return false;
        }

        $card      = array('id'     => intval($_REQUEST['id']),
                           'number' => $_REQUEST['number']
                           );

        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
        $result = $SipSettings->SipPort->addBalanceFromVoucher($SipSettings->sipId,$card);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            $return=array('success'       => false,
                          'error_message' => $_msg
                          );
            print (json_encode($return));
            return false;
        }  else {
            $return=array('success'       => true,
                          'error_message' => 'Added balance succeeded'
                          );
            print (json_encode($return));
            return true;
        }

    } else if ($_REQUEST['action'] == 'get_identity'){
        $account=array('sip_address'       => $SipSettings->account,
                       'email'             => $SipSettings->email,
                       'first'             => $SipSettings->firstName,
                       'lastname'          => $SipSettings->lastName,
                       'pstn_caller_id'    => $SipSettings->rpid,
                       'mobile_number'     => $SipSettings->mobile_number,
                       'timezone'          => $SipSettings->timezone,
                       'no_answer_timeout' => $SipSettings->timeout,
                       'quick_dial'        => $SipSettings->quickdial
                       );
        print json_encode($account);
        return true;
    } else if ($_REQUEST['action'] == 'get_devices'){
        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
        $result     = $SipSettings->SipPort->getSipDeviceLocations(array($SipSettings->sipId));

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            $_ret=false;
            $return=array('success'       => $_ret,
                          'error_message' => $_msg
                          );
            print (json_encode($return));
            return false;
        }  else {
            foreach ($result[0]->locations as $locationStructure) {
                $contact=$locationStructure->address.":".$locationStructure->port;
                if ($locationStructure->publicAddress) {
                    $publicContact=$locationStructure->publicAddress.":".$locationStructure->publicPort;
                } else {
                    $publicContact=$contact;
                }
                $devices[]=array("contact"       => $contact,
                                         "publicContact" => $publicContact,
                                         "expires"       => $locationStructure->expires,
                                         "user_agent"    => $locationStructure->userAgent,
                                         "transport"     => $locationStructure->transport
                                     );
            }
        }

        print (json_encode($devices));
        return true;

    } else if ($_REQUEST['action'] == 'set_dnd_on'){
        $SipSettings->getAcceptRules();
        $SipSettings->acceptRules['temporary']=array('groups'   =>array('nobody'),
                                                     'duration' =>intval($_REQUEST['duration'])
                                                     );
        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
        $result     = $SipSettings->SipPort->setAcceptRules($SipSettings->sipId,$SipSettings->acceptRules);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            $_ret=false;
        } else {
            $_ret=true;
            if (intval($_REQUEST['duration'] > 0)) {
                $_msg=sprintf(_('Do not disturb has been enabled for %d minutes'),intval($_REQUEST['duration']));
            } else {
                $_msg=sprintf(_('Do not disturb has been enabled'));
            }
        }

        $return=array('success'       => $_ret,
                      'error_message' => $_msg
                      );
        print (json_encode($return));
        return true;
    } else if ($_REQUEST['action'] == 'set_dnd_off'){
        $SipSettings->getAcceptRules();
        $SipSettings->acceptRules['temporary']=array('groups'   =>array('everybody'),
                                                     'duration' =>0
                                                     );
        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
        $result     = $SipSettings->SipPort->setAcceptRules($SipSettings->sipId,$SipSettings->acceptRules);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            $_ret=false;
        } else {
            $_ret=true;
            $_msg=sprintf(_('Do not disturb has been disabled'));
        }

        $return=array('success'       => $_ret,
                      'error_message' => $_msg
                      );
        print (json_encode($return));
        return true;
    } else if ($_REQUEST['action'] == 'set_privacy_on'){
        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
        $result     = $SipSettings->SipPort->addToGroup(array("username" => $SipSettings->username,"domain"=> $SipSettings->domain),"anonymous");

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            $_ret=false;
        } else {
            $_ret=true;
            $_msg=sprintf(_('Caller-ID is now hidden for outgoing calls'));
        }

        $return=array('success'       => $_ret,
                      'error_message' => $_msg
                      );
        print (json_encode($return));
        return true;
    } else if ($_REQUEST['action'] == 'set_privacy_off'){
        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
        $result     = $SipSettings->SipPort->removeFromGroup(array("username" => $SipSettings->username,"domain"=> $SipSettings->domain),"anonymous");

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode == 1031) {
                $_ret=true;
                $_msg=sprintf(_('Caller-ID is now visible for outgoing calls'));
            } else {
                $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                $_ret=false;
            }
        } else {
            $_ret=true;
            $_msg=sprintf(_('Caller-ID is now visible for outgoing calls'));
        }

        $return=array('success'       => $_ret,
                      'error_message' => $_msg
                      );
        print (json_encode($return));
        return true;
    } else if ($_REQUEST['action'] == 'add_alias'){
        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);

        $username=trim($_REQUEST['username']);

        if (!strlen($username)) {
            $return=array('success'       => false,
                          'error_message' => 'Error: missing username'
                          );
            print (json_encode($return));
            return false;
        }

        $_aliasObject=array("id"=>array("username"=>strtolower($username),
                                        "domain"=>$SipSettings->domain
                                        ),
                            "owner" => intval($SipSettings->owner),
                            "target"=> array("username" => $SipSettings->username,"domain"=> $SipSettings->domain)
                            )
                            ;

        $result     = $SipSettings->SipPort->addAlias($_aliasObject);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            $_ret=false;
        } else {
            $_ret=true;
            $_msg=sprintf(_('Added alias %s'),strtolower($username));
        }

        $return=array('success'       => $_ret,
                      'error_message' => $_msg
                      );
        print (json_encode($return));
        return true;
    } else if ($_REQUEST['action'] == 'set_call_forwarding') {
        $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
        $result     = $SipSettings->SipPort->getCallDiversions($SipSettings->sipId);

        if (PEAR::isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            $_ret=false;
            $return=array('success'       => $_ret,
                          'error_message' => $_msg
                          );
            print (json_encode($return));
            return true;

        }

        $SipSettings->getVoicemail();

        foreach(array_keys($SipSettings->diversionType) as $condition) {
            $old_diversions[$condition]=$result->$condition;
        }

		$_log='';
        foreach(array_keys($old_diversions) as $key) {

            if (isset($_REQUEST[$key])) {
                printf ("Key $key changed %s",$_REQUEST[$key]);
	        	$textboxURI=$_REQUEST[$key];

				if ($textboxURI == "<mobile-number>" && strlen($SipSettings->mobile_number)) {
                	$textboxURI = $SipSettings->mobile_number;
                }

                if ($textboxURI && $textboxURI != "<voice-mailbox>" && !preg_match("/@/",$textboxURI)) {
                    $textboxURI=$textboxURI."@".$SipSettings->domain;
                }


                if (preg_match("/^([\+|0].*)@/",$textboxURI,$m))  {
                    $textboxURI=$m[1]."@".$SipSettings->domain;
                }

                if (strlen($textboxURI) && $textboxURI != "<voice-mailbox>" && !preg_match("/^sip:/",$textboxURI))  {
                    $textboxURI='sip:'.$textboxURI;
                }

                if ($textboxURI) {
                    $new_diversions[$key]=$textboxURI;
                }

                $_log.=sprintf("%s=%s ",$key,$textboxURI);
                $divert_changed=true;

            } else {
                if ($old_diversions[$key]) {
            		$new_diversions[$key]=$old_diversions[$key];
                }
            }
        }

        if ($divert_changed) {
            $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
            $result     = $SipSettings->SipPort->setCallDiversions($SipSettings->sipId,$new_diversions);
    
            if (PEAR::isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                $_ret=false;
            } else {
                $_ret=true;
                $_msg=sprintf(_('Changed diversions %s'),$_log);
            }

            $return=array('success'       => $_ret,
                          'error_message' => $_msg
                          );
            print (json_encode($return));
            return true;
        } else {
            $return=array('success'       => true,
                          'error_message' => 'Diversions remained the same'
                          );
            print (json_encode($return));
            return true;
        }

    } else if ($_REQUEST['action']) {
        $return=array('success'       => false,
                      'error_message' => "Error: invalid action"
                      );
        print (json_encode($return));
        return false;
    }

    if (!$_REQUEST['export']) {
        $SipSettings->showAccount();
    
        print "
        </body>
        </html>
        ";
    }

}

class Enrollment {
    var $init=false;
    var $create_voicemail           = false;
    var $send_email_notification    = true;
    var $create_email_alias         = false;
	var $create_customer            = true;
    var $timezones                  = array();
    var $default_timezone           = 'Europe/Amsterdam';

    function Enrollment() {

        include("/etc/cdrtool/enrollment/config.ini");
        include("/etc/cdrtool/ngnpro_engines.inc");

    	$this->soapEngines  = $soapEngines;
        $this->enrollment   = $enrollment;

		$this->loadTimezones();

        if (!is_array($this->soapEngines)) {
            $return=array('success'       => false,
                          'error_message' => 'Error: Missing soap engines configuration'
                          );
            print (json_encode($return));
            return false;
        }

        if (!is_array($this->enrollment)) {
            $return=array('success'       => false,
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

		$this->reseller       = $this->enrollment['reseller'];
        $this->outbound_proxy = $this->enrollment['outbound_proxy'];
        $this->xcap_root      = $this->enrollment['xcap_root'];
        $this->msrp_relay     = $this->enrollment['msrp_relay'];
        $this->settings_url   = $this->enrollment['settings_url'];

        if ($this->enrollment['sip_class']) {
        	$this->sipClass = $this->enrollment['sip_class'];
        } else {
        	$this->sipClass = 'SipSettings';
        }

        if (!$this->sipEngine) {
            $return=array('success'       => false,
                          'error_message' => 'Missing sip engine'
                          );
            print (json_encode($return));
            return false;
        }

        if (!$this->sipDomain) {
            $return=array('success'       => false,
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

    function createAccount() {

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

		$username=strtolower(trim($_REQUEST['username']));

        if (!preg_match("/^[1-9a-z][0-9a-z_.-]{2,64}[0-9a-z]$/",$username)) {
            $return=array('success'       => false,
                          'error'         => 'value_error',
                          'error_message' => 'The username must contain at least 4 lowercase alpha-numeric . _ or - characters and must start and end with a positive digit or letter'
                          );
            print (json_encode($return));
            return false;
        }

        $sip_address=$username.'@'.$this->sipDomain;

        if ($this->create_customer && !$_REQUEST['owner']) {
        	// create owner id

            $customerEngine           = 'customers@'.$this->customerEngine;
            $this->CustomerSoapEngine = new SoapEngine($customerEngine,$this->soapEngines,$this->customerLoginCredentials);
            $_customer_class          = $this->CustomerSoapEngine->records_class;
            $this->customerRecords    = new $_customer_class(&$this->CustomerSoapEngine);
            $this->customerRecords->html=false;
    
            $properties=$this->customerRecords->setInitialCredits(array('sip_credit'         => 1,
                                                                        'sip_alias_credit'   => 1,
                                                                        'email_credit'       => 1
                                                                        )
                                                                  );
            if (preg_match("/^(\w+)\s+(\w+)$/",$_REQUEST['display_name'],$m)) {
                $firstName = $m[1];
                $lastName  = $m[2];
            } else {
                $firstName = $_REQUEST['display_name'];
                $lastName  = 'Blink';
            }

            $timezone=$_REQUEST['tzinfo'];

            if (!in_array($timezone, $this->timezones)) {
            	$timezone=$this->default_timezone;
            }

            $customer=array(
                         'firstName'  => $firstName,
                         'lastName'   => $lastName,
                         'timezone'   => $timezone,
                         'password'   => trim($_REQUEST['password']),
                         'email'      => trim($_REQUEST['email']),
                         'properties' => $properties
                        );
    
            $_customer_created=false;

            $j=0;
    
            while ($j < 3) {
    
                $username.=RandomString(4);
    
                $customer['username']=$username;
    
                if (!$result = $this->customerRecords->addRecord($customer)) {
                    if ($this->customerRecords->SoapEngine->exception->errorcode != "5001") {
                        $return=array('success'       => false,
                        	          'error'         => 'internal_error',
                                      'error_message' => 'failed to create non-duplicate customer entry'
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
            }
    
            $owner=$result->id;
    
            if (!$owner) {
                $return=array('success'       => false,
                              'error'         => 'internal_error',
                              'error_message' => 'failed to obtain a new owner id'
                              );
                print (json_encode($return));
                return false;
            }
        } else if (is_numeric($_REQUEST['owner']) && $_REQUEST['owner'] != 0 ) {
            $owner=intval($_REQUEST['owner']);
        } else {
                $return=array('success'       => false,
                              'error'         => 'internal_error',
                              'error_message' => 'no owner information provided'
                              );
                print (json_encode($return));
                return false;
        }

        // create SIP Account
        $sipEngine           = 'sip_accounts@'.$this->sipEngine;

        $this->SipSoapEngine = new SoapEngine($sipEngine,$this->soapEngines,$this->sipLoginCredentials);
        $_sip_class          = $this->SipSoapEngine->records_class;
        $this->sipRecords    = new $_sip_class(&$this->SipSoapEngine);
        $this->sipRecords->html=false;


        $sip_properties[]=array('name'=> 'ip',                 'value' => $_SERVER['REMOTE_ADDR']);
        $sip_properties[]=array('name'=> 'registration_email', 'value' => $_REQUEST['email']);

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
                            'timezone'  => $timezone,
                            'prepaid'   => 1,
                            'pstn'      => 1,
                            'quota'     => 50,
                            'owner'     => intval($owner),
                            'groups'    => $this->groups,
                            'properties'=> $sip_properties
                            );

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

        	if (!$passport = $this->generateCertificate($sip_address,$_REQUEST['email'],$_REQUEST['password'])) {
                $return=array('success'       => false,
                              'error'         => 'internal_error',
                              'error_message' => 'failed to generate certificate'
                              );
                print (json_encode($return));
                return false;
            }

            // Generic code for all sip settings pages

            if ($this->create_voicemail || $this->send_email_notification) {
                if ($SipSettings = new $this->sipClass($sip_address,$this->sipLoginCredentials,$this->soapEngines)) {
    
                    if ($this->create_voicemail) {
                        // Add voicemail account
                        $SipSettings->addVoicemail();
                        $SipSettings->setVoicemailDiversions();
                    }
                    if ($this->send_email_notification) {
                        // Sent account settings by email
                        $SipSettings->sendEmail('hideHtml');
                    }
                }
            }

            if ($this->create_email_alias) {
                $emailEngine           = 'email_aliases@'.$this->emailEngine;
                $this->EmailSoapEngine = new SoapEngine($emailEngine,$this->soapEngines,$this->sipLoginCredentials);
                $_email_class          = $this->EmailSoapEngine->records_class;
                $this->emailRecords    = new $_email_class(&$this->EmailSoapEngine);
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
                          'mdns_username'  => $customer['username'],
                          'mdns_password'  => $customer['password'],
                          'mdns_id'        => intval($owner),
                          'email'          => $result->email,
                          'passport'       => $passport,
                          'outbound_proxy' => $this->outbound_proxy,
                          'xcap_root'      => $this->xcap_root,
                          'msrp_relay'     => $this->msrp_relay,
                          'settings_url'   => $this->settings_url
                          );

            print (json_encode($return));

            return true;
        }
    }

    function generateCertificate($sip_address,$email,$password) {
        if (!$this->init) return false;

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

		if ($this->crt==FALSE) {
			while (($e = openssl_error_string()) !== false) {
				echo $e . "\n";
				print "<br><br>";
			}
            return false;
		}

        openssl_x509_export   ($this->crt, $this->crt_out);
        openssl_pkcs12_export ($this->crt, $this->pk12_out, $this->key, $password);

        return array(
                     'crt'  => $this->crt_out,
                     'key'  => $this->key_out,
                     'pk12' => $this->pk12_out,
                     'ca'   => file_get_contents($this->enrollment['ca_crt'])
                     );
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

    function loadTimezones () {
        if (!$fp = fopen("timezones", "r")) {
        	syslog(LOG_NOTICE, 'Error: Failed to open timezones file');
        	return false;
        }
        while ($buffer = fgets($fp,1024)) {
            $this->timezones[]=trim($buffer);
        }

        fclose($fp);
    }

}

class PaypalProcessor {
    var $deny_countries     = array();
	var $allow_countries    = array();
	var $deny_ips           = array();
    var $make_credit_checks = false;

    function PaypalProcessor($account) {
        if (!is_object($account)) {
            return false;
        }
        
        require('cc_processor.php');
        
        if ($this->fraudDetected(&$account)) {
            $chapter=sprintf(_("Payments"));
            $account->showChapter($chapter);
            
            print "
            <tr>
            <td colspan=3>
            ";
            
            if ($account->login_type!='subscriber') {
                print "<p>";
                printf ("<font color=red>%s</font>",$this->fraud_reason);
            } else {
                print _("Page Not Available");
                $log=sprintf("CC transaction is not allowed from %s for %s (%s)",$_SERVER['REMOTE_ADDR'],$account->account,$this->fraud_reason);
                syslog(LOG_NOTICE, $log);
            }
            
            print "</td>
            </tr>
            ";
            
            return false;
        }
        
        $CardProcessor = new CreditCardProcessor();
        
        if (is_array($this->test_credit_cards) && in_array($_POST['creditCardNumber'], $this->test_credit_cards)) {
	        $CardProcessor->environment='sandbox';
        }
        
        $CardProcessor->chapter_class  = 'chapter';
        $CardProcessor->odd_row_class  = 'odd';
        $CardProcessor->even_row_class = 'even';
        
        $CardProcessor->note = $account->account;
        $CardProcessor->account = $account->account;
        
        // set hidden elements we need to preserve in the shopping cart application
        $CardProcessor->hidden_elements = $account->hiddenElements;
        
        // load shopping items
        $CardProcessor->cart_items = array(
        'pstn_credit'=>array('price'       => 30,
        'description' => _('PSTN Credit'),
        'unit'        => 'credit',
        'duration'    => 'N/A',
        'qty'         => 1
        )
        );
        
        // load user information from owner information if available otherwise from sip account settings
        
        if ($account->owner_information['firstName']) {
	    	$CardProcessor->user_account['FirstName']=$account->owner_information['firstName'];
    	} else {
        	$CardProcessor->user_account['FirstName']=$account->firstName;
        }
        
        if ($account->owner_information['lastName']) {
        	$CardProcessor->user_account['LastName']=$account->owner_information['lastName'];
        } else {
        	$CardProcessor->user_account['LastName']=$account->lastName;
        }
        
        if ($account->owner_information['email']) {
        	$CardProcessor->user_account['Email']=$account->owner_information['email'];
        } else {
        	$CardProcessor->user_account['Email']=$account->email;
        }
        
        if ($account->owner_information['address'] && $account->owner_information['address']!= 'Unknown') {
        	$CardProcessor->user_account['Address1']=$account->owner_information['address'];
        } else {
        	$CardProcessor->user_account['Address1']='';
        }
        
        if ($account->owner_information['city'] && $account->owner_information['city']!= 'Unknown') {
        	$CardProcessor->user_account['City']=$account->owner_information['city'];
        } else {
        	$CardProcessor->user_account['City']='';
        }
        
        if ($account->owner_information['country'] && $account->owner_information['country']!= 'Unknown') {
        	$CardProcessor->user_account['Country']=$account->owner_information['country'];
        } else {
	        $CardProcessor->user_account['Country']='';
        }
        
        if ($account->owner_information['state'] && $account->owner_information['state']!= 'Unknown') {
    	    $CardProcessor->user_account['State']=$account->owner_information['state'];
        } else {
        	$CardProcessor->user_account['State']='';
        }
        
        if ($account->owner_information['postcode'] && $account->owner_information['postcode']!= 'Unknown') {
	        $CardProcessor->user_account['PostCode']=$account->owner_information['postcode'];
        } else {
    	    $CardProcessor->user_account['PostCode']='';
        }
        
        if ($_REQUEST['purchase'] == '1' ) {
            $chapter=sprintf(_("Transaction Results"));
            $account->showChapter($chapter);
            
            print "
            <tr>
            <td colspan=3>
            ";
            
            // ensure that submit requests are coming only from the current page
            if ($_SERVER['HTTP_REFERER'] == $CardProcessor->getPageURL()) {
                    
                // check submitted values
                $formcheck1 = $CardProcessor->checkForm($_POST);
                if (count($formcheck1) > 0){
                    // we have errors; let's print and stop
                    print $CardProcessor->displayProcessErrors($formcheck1);
                    return false;
                }
                
                // process the payment
                $b=time();
                
                $pay_process_results = $CardProcessor->processPayment($_POST);
                //print_r($pay_process_results);
                if(count($pay_process_results['error']) > 0){
                    // there was a problem with payment
                    // show error and stop
                    
                    if ($pay_process_results['error']['field'] == 'reload') {
                        print $pay_process_results['error']['desc'];
                    } else {
                        print $CardProcessor->displayProcessErrors($pay_process_results['error']);
                    }
                    
                    $e=time();
                    $d=$e-$b;
                    
                    $log=sprintf("Error, CC transaction failed for %s: %s (%s) after %d seconds",
                    $account->account,
                    $pay_process_results['error']['short_message'],
                    $pay_process_results['error']['error_code'],
                    $d
                    );
                    
                    syslog(LOG_NOTICE, $log);
                    
                    return false;
                } else {
                
                    $e=time();
                    $d=$e-$b;
                    
                    $log=sprintf("CC transaction for %s %s completed succesfully in %d seconds",
                    $account->account,
                    $pay_process_results['success']['desc']->TransactionID,
                    $d
                    );
                    syslog(LOG_NOTICE, $log);
                    
                    print "<p>";
                    print _("Transaction completed sucessfully. ");

                    /*
                    if ($CardProcessor->environment!='sandbox' && $account->first_transaction) {
                        print "<p>";
                        print _("This is your first payment. ");
                        
                        print "<p>";
                        print _("Please allow the time to check the validity of your transaction before activating your Credit. ");
                        
                        print "<p>";
                        print _("You can speed up the validation process by sending a copy of an utility bill (electriciy, gas or TV) that displays your address. ");
                        
                        print "<p>";
                        printf (_("For questions related to your payments or to request a refund please email to <i>%s</i> and mention your transaction id <b>%s</b>. "),
                        $account->billing_email,
                        $pay_process_results['success']['desc']->TransactionID
                        );
                        
                        $this->make_credit_checks=true;
                    
                    } else {
                       print "<p>";
                       print _("You may check your new balance in the Credit tab. ");
                    }
                    */
                    print "<p>";
                    print _("You may check your new balance in the Credit tab. ");
                }
                
                if ($account->Preferences['ip'] && $_loc=geoip_record_by_name($account->Preferences['ip'])) {
                    $registration_location=$_loc['country_name'].'/'.$_loc['city'];
                } else if ($account->Preferences['ip'] && $_loc=geoip_country_name_by_name($account->Preferences['ip'])) {
                    $registration_location=$_loc;
                } else {
                    $registration_location='Unknown';
                }
                
                if ($_loc=geoip_record_by_name($_SERVER['REMOTE_ADDR'])) {
                    $transaction_location=$_loc['country_name'].'/'.$_loc['city'];
                } else if ($_loc=geoip_country_name_by_name($_SERVER['REMOTE_ADDR'])) {
                    $transaction_location=$_loc;
                } else {
                    $transaction_location='Unknown';
                }
                
                if ($account->Preferences['timezone']) {
                    $timezone=$account->Preferences['timezone'];
                } else {
                    $timezone='Unknown';
                }
                
                $extra_information=array('SIP account' => $account->admin_url_absolute,
                'SIP Account Timezone'  => $account->timezone,
                'Registration IP'       => $account->Preferences['ip'],
                'Registration Location' => $registration_location,
                'Registration Email'    => $account->Preferences['registration_email'],
                'Registration Timezone' => $timezone,
                'Transaction Location'  => $transaction_location
                );
                
                if ($this->make_credit_checks) {
                    $extra_information['First Payment']='Yes';
                }
                
                if ($CardProcessor->saveOrder($_POST,$pay_process_results,$extra_information)) {
                
                    // add PSTN credit
                    $description=sprintf("CC transaction %s",$CardProcessor->transaction_data['TRANSACTION_ID']);
                    $account->addBalanceReseller($CardProcessor->transaction_data['TOTAL_AMOUNT'],$description);
                    
                    $account->addInvoice($CardProcessor);
                    
                    return true;
                
                } else {
                    $log=sprintf("Error: SIP Account %s - CC transaction %s failed to Save Order",$account->account, $CardProcessor->transaction_data['TRANSACTION_ID']);
                    syslog(LOG_NOTICE, $log);
                    //print _("Error Saving Order");
                    return false;
                }
                
                
            } else {
                print _("Invalid Request");
                return false;
            }
            
            print "
            </td>
            </tr>
            ";
        
        } else {
            $chapter=sprintf(_("Add Credit"));
            $account->showChapter($chapter);
            
            print "
            <tr>
            <td colspan=3>
            ";
            
            print "<p>";
            print _("Add balance to your Credit by purchasing it with a Credit Card. ");
            // print the submit form
            $arr_form_page_objects = $CardProcessor->showSubmitForm();
            print $arr_form_page_objects['page_body_content'];
            
            print "
            </td>
            </tr>
            ";
            
    	}
    
	}

    function fraudDetected ($account) {

        if (count($this->deny_ips)) {
            foreach ($this->deny_ips as $_ip) {
                if ($account->Preferences['ip'] && preg_match("/^$_ip/",$account->Preferences['ip'])) {
                    $this->fraud_reason=$account->Preferences['ip'].' is Blocked';
                    return true;
                }

                if (preg_match("/^$_ip/",$_SERVER['REMOTE_ADDR'])) {
                    $this->fraud_reason=$_SERVER['REMOTE_ADDR'].' is a Blocked';
                    return true;
                }
            }
        }

        if (count($this->deny_countries)) {

            if ($_loc=geoip_record_by_name($account->Preferences['ip'])) {
                if (in_array($_loc['country_name'],$this->deny_countries)) {
                    $this->fraud_reason=$_loc['country_name'].' is Blocked';
                    return true;
                }
            }
        }

        if (count($this->allow_countries)) {
            if ($_loc=geoip_record_by_name($account->Preferences['ip'])) {
                if (!in_array($_loc['country_name'],$this->allow_countries)) {
                    $this->fraud_reason=$_loc['country_name'].' is Not Allowed';
                    return true;
                }
            }
        }


        if (count($this->deny_email_domains)) {
            if (count($this->accept_email_addresses)) {
                if (in_array($account->email,$this->accept_email_addresses)) return false;
            }

            list($user,$domain)= explode("@",$account->email);
            foreach ($this->deny_email_domains as $deny_domain) {
                if ($domain == $deny_domain) {
                    $this->fraud_reason=sprintf ('Domain %s is Not Allowed',$domain);
                    return true;

                }
            }
        }

        return false;
    }

}

if (file_exists("/etc/cdrtool/local/sip_settings.php")) {
	require_once('/etc/cdrtool/local/sip_settings.php');
}

?>
