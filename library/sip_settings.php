<?php
/**
 * Copyright (c) 2007-2019 AG Projects
 * http://ag-projects.com
 * Author Adrian Georgescu
 *
 * This library provide the functions for managing properties
 * of SIP Accounts retrieved from NGNPro
 *
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
    var $xcap_root                 = "https://cdrtool.example.com/xcap-root";
    var $pstn_access               = false;
    var $sms_access                = false;
    var $pstn_changes_allowed      = false;
    var $prepaid_changes_allowed   = false;
    var $sip_proxy                 = "proxy.example.com";
    var $voicemail_server          = "vm.example.com";
    var $absolute_voicemail_uri    = false; // use <voice-mailbox>
    var $enable_thor               = false;
    var $currency                  = "&euro;";
    // access numbers

    var $voicemail_access_number        = "1233";
    var $FUNC_access_number             = "*21*";
    var $FNOL_access_number             = "*22*";
    var $FNOA_access_number             = "*23*";
    var $FBUS_access_number             = "*23*";
    var $change_privacy_access_number   = "*67";
    var $check_privacy_access_number    = "*68";
    var $reject_anonymous_access_number = "*69";

    var $show_barring_tab   = false;
    var $show_payments_tab  = false;
    var $show_tls_section   = false;
    var $show_support_tab   = false;
    var $show_did_tab       = false;
    var $show_directory     = false;

    var $notify_on_sip_account_changes  = false;

    var $first_tab          = 'calls';
    var $auto_refesh_tab    = 0;              // number of seconds after which to refresh tab content in the web browser

    var $payment_processor_class = false;
    var $did_processor_class = false;

    var $show_download_tab    = 'Blink';     // set it to name of the tab or false to disable it
    var $digest_settings_page = "https://blink.sipthor.net/settings.phtml";

    // end variables

    var $tab                       = "settings";
    var $phonebook_img             = "<img src=images/pb.gif border=0>";
    var $call_img                  = "<div style=\"font-size: 14px;\"><i class=\"icon-phone\"></i>";
    var $delete_img                = "<img src=images/del_pb.gif border=0 alt='Delete'>";
    var $plus_sign_img             = "<img src=images/plus_sign.png border=0 alt='Add Contact'>";
    var $embedded_img              = "<img src=images/blink.png border=0>";

    var $groups                    = array();

    var $form_elements             = array(
                                           'mailto',
                                           'free-pstn',
                                           'blocked',
                                           'sip_password',
                                           'web_password',
                                           'yubikey',
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
                                           'web_password_reset',
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
                                           'show_barring_tab',
                                           'ip_access_list',
                                           'callLimit'
                                           );

	var $disable_extra_groups=true;

    var $prepaid             = 0;
    var $emergency_regions   = array();
    var $FNOA_timeoutDefault = 35;
    var $enums               = array();
    var $barring_prefixes    = array();
    var $SipUAImagesPath     = "images";
    var $SipUAImagesFile     = "phone_images.php";
    var $balance_history     = array();
    var $enrollment_url      = false;
    var $sip_settings_api_url= false;
    var $journalEntries      = array();
    var $chat_replication_backend = 'mysql';   // mongo or mysql
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

    var $pstn_termination_price_page = 'sip_rates_body.html';
    var $append_domain_to_xcap_root = false;
    var $blink_download_url   = "https://blink.sipthor.net/download.phtml?download";
    var $ownerCredentials = array();
    var $localGroups = array();
    var $max_credit_per_day = 40;
    var $enrollment_configuration = "/etc/cdrtool/enrollment/config.ini";
    var $require_proof_of_identity = true;
    var $call_limit_may_by_changed_by = 'reseller'; #subscriber, reseller, admin
    var $ip_access_list_may_by_changed_by = 'reseller'; #subscriber, reseller, admin
    var $create_certificate = false;

    function SipSettings($account,$loginCredentials=array(),$soapEngines=array()) {

		//define_syslog_variables();

        $this->platform_call_limit = _('unlimited');

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

        $this->reseller           = $loginCredentials['reseller'];
        $this->customer           = $loginCredentials['customer'];

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

        } else if ($this->login_type == "reseller" || $this->login_type == "customer") {

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

        $this->availableGroups['deny-password-change']  = array("Group"=>"deny-password-change",
                                        "WEBName" =>sprintf(_("Deny password change")),
                                        "SubscriberMayEditIt"=>0,
                                        "SubscriberMaySeeIt"=>0,
                                        "ResellerMayEditIt"=>1,
                                        "ResellerMaySeeIt"=>1
                                        );

        $this->getResellerSettings();
        $this->getCustomerSettings();

        if ($this->reject_anonymous_access_number) {
            $_comment = sprintf(_("Dial %s to change"), $this->reject_anonymous_access_number);
        } else {
            $_comment = '';
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


        $this->availableGroups=array_merge($this->availableGroups, $this->localGroups);

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
            if ($this->show_barring_tab || $this->Preferences['show_barring_tab']) {
            	$this->tabs['barring']=_("Barring");
            }
        }

        if ($this->show_did_tab) {
            $this->tabs['did']=_("DID");
        }

	if (!$this->isEmbedded() && $this->show_download_tab) {
            $this->tabs['download'] = $this->show_download_tab;
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

        if ($this->prepaid && $this->pstn_access) {
            $this->tabs['credit']=_("Credit");
        }

	$_protocol=preg_match("/^(https?:\/\/)/",$_SERVER['SCRIPT_URI'],$m);
        $this->absolute_url=$m[1].$_SERVER['HTTP_HOST'].$this->url;

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

        if ($this->soapEngines[$this->sip_engine]['chat_replication_backend']) {
            $this->chat_replication_backend = $this->soapEngines[$this->sip_engine]['chat_replication_backend'];
        }

        if ($this->soapEngines[$this->sip_engine]['mongo_db']) {
            $this->mongo_db = $this->soapEngines[$this->sip_engine]['mongo_db'];
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
                                  "impersonate" => intval($this->customer)
                                  );

            $this->soapUsername = $this->soapEngines[$this->sip_engine]['username'];
        }

        $this->SoapAuth = array('auth', $this->SOAPlogin , 'urn:AGProjects:NGNPro', 0, '');

        //print_r($this->SoapAuth);

        $this->SOAPloginAdmin = array(
                               "username"    => $this->soapEngines[$this->sip_engine]['username'],
                               "password"    => $this->soapEngines[$this->sip_engine]['password'],
                               "admin"       => true
                               );

        $this->SoapAuthAdmin = array('auth', $this->SOAPloginAdmin , 'urn:AGProjects:NGNPro', 0, '');

        if (strlen($this->loginCredentials['customer_engine'])) {
            $this->customer_engine=$this->loginCredentials['customer_engine'];
        } else if (strlen($this->soapEngines[$this->sip_engine]['customer_engine'])) {
            $this->customer_engine=$this->soapEngines[$this->sip_engine]['customer_engine'];
        } else {
            $this->customer_engine=$this->sip_engine;
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

        if (strlen($this->soapEngines[$this->sip_engine]['digest_settings_page'])) {
            $this->digest_settings_page = $this->soapEngines[$this->sip_engine]['digest_settings_page'];
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

        if ($this->soapEngines[$this->sip_engine]['emergency_regions']) {
            $this->emergency_regions  = $this->soapEngines[$this->sip_engine]['emergency_regions'];
        }

        if ($this->soapEngines[$this->sip_engine]['pstn_access']) {
            $this->pstn_access     = $this->soapEngines[$this->sip_engine]['pstn_access'];
        }

        if ($this->soapEngines[$this->sip_engine]['call_limit']) {
            $this->platform_call_limit    = $this->soapEngines[$this->sip_engine]['call_limit'];
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

        if (isset($this->soapEngines[$this->sip_engine]['show_download_tab'])) {
            $this->show_download_tab=$this->soapEngines[$this->sip_engine]['show_download_tab'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['show_barring_tab'])) {
            $this->show_barring_tab=$this->soapEngines[$this->sip_engine]['show_barring_tab'];
        }

        if (isset($this->soapEngines[$this->sip_engine]['disable_extra_groups'])) {
            $this->disable_extra_groups=$this->soapEngines[$this->sip_engine]['disable_extra_groups'];
        }

        if (strlen($this->soapEngines[$this->sip_engine]['notify_on_sip_account_changes'])) {
            //dprint($this->soapEngines[$this->sip_engine]['notify_on_sip_account_changes']);
            $this->notify_on_sip_account_changes=$this->soapEngines[$this->sip_engine]['notify_on_sip_account_changes'];
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
                               "impersonate" => intval($this->customer)
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
                               "impersonate" => intval($this->customer)
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
                               "impersonate" => intval($this->customer)
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
    }

    function getMongoJournalTable() {
        $this->mongo_table_ro = NULL;
        $this->mongo_table_rw = NULL;
        $this->mongo_exception = 'Mongo exception';

        if (is_array($this->mongo_db)) {
            $mongo_uri        = $this->mongo_db['uri'];
            $mongo_replicaSet = $this->mongo_db['replicaSet'];
            $mongo_database   = $this->mongo_db['database'];
            $mongo_table      = $this->mongo_db['table'];
            try {
                $mongo_connection = new Mongo("mongodb://$mongo_uri", array("replicaSet" => $mongo_replicaSet));
                $mongo_db = $mongo_connection->selectDB($mongo_database);
                $this->mongo_table_ro = $mongo_db->selectCollection($mongo_table);
                $this->mongo_table_ro->setSlaveOkay(true);
                $this->mongo_table_rw = $mongo_db->selectCollection($mongo_table);
                return true;
            } catch (MongoException $e) {
                $this->mongo_exception=$e->getMessage();
                return false;
            } catch (MongoConnectionException $e) {
                $this->mongo_exception=$e->getMessage();
                return false;
            } catch (Exception $e) {
                $this->mongo_exception=$e->getMessage();
                return false;
            }
        }
        return false;
    }

    function getAccount($account) {
        dprint("getAccount($account, engine=$this->sip_engine)");

        list($username,$domain)=explode("@",trim($account));

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getAccount(array("username" =>$username,"domain"   =>$domain));

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        //print "<pre>";
        dprint_r($result);
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

        if ( $this->Preferences['blocked_by'] && !in_array("blocked",$result->groups)) {
            $this->Preferences['blocked_by']='';
        }

        $this->username       = $result->id->username;
        $this->domain         = $result->id->domain;
        $this->password       = $result->password;
        $this->firstName      = $result->firstName;
        $this->lastName       = $result->lastName;
        $this->rpid           = $result->rpid;
        $this->owner          = $result->owner;
        $this->timezone       = $result->timezone;
        $this->email          = $result->email;
        $this->groups         = $result->groups;
        $this->createDate     = $result->createDate;
        $this->web_password   = $this->Preferences['web_password'];
        $this->quickdial      = $result->quickdialPrefix;
        $this->timeout        = intval($result->timeout);
        $this->quota          = $result->quota;
        $this->prepaid        = intval($result->prepaid);
        $this->region         = $result->region;

        $this->account        = $this->username."@".$this->domain;
        $this->fullName       = $this->firstName." ".$this->lastName;
        $this->name           = $this->firstName; // used by smarty

        $this->yuibikey         = $result->Preferences['yubikey'];

        if ($this->soapEngines[$this->sip_engine]['call_limit']) {
            if ($result->callLimit) {
                $this->callLimit   = $result->callLimit;
            } else  {
                $this->callLimit = '';
            }
        }

        if ($this->soapEngines[$this->sip_engine]['ip_access_list']) {
            if (is_array($result->acl) and count($result->acl)) {
                foreach (array_keys($result->acl) as $key) {
                    $this->ip_access_list .= sprintf("%s/%s ",$result->acl[$key]->ip, $result->acl[$key]->mask);
                }
                $this->ip_access_list = trim($this->ip_access_list);
            } else  {
                $this->ip_access_list = $this->soapEngines[$this->sip_engine]['ip_access_list'];
            }
        }

        $this->sipId=array("username" => $this->username,
                           "domain" => $this->domain
                           );

        if (!$this->timeout) {
            $this->timeoutWasNotSet=1;
            $this->timeout=intval($this->FNOA_timeoutDefault);
        }

        if ($this->timeout > 900 ) {
            $this->timeoutWasNotSet=1;
            $this->timeout=intval(900);
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }

        //dprint_r($result);
        foreach ($result->aliases as $_alias) {
            $this->aliases[]=$_alias->id->username.'@'.$_alias->id->domain;
        }

    }

    function getRatingEntityProfiles() {
        dprint("getRatingEntityProfiles()");

        $this->EntityProfiles=array();

        $this->RatingPort->addHeader($this->SoapAuthRating);
        $entity="subscriber://".$this->username."@".$this->domain;
        $result     = $this->RatingPort->getEntityProfiles($entiry);

        if ((new PEAR)->isError($result)) {
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

            if ((new PEAR)->isError($result)) {
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

            if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        print "<div class='row-fluid'>
            <div class='alert alert-info span12' style='min-height:10px'>
            <div class='row-fluid'>
            <span style='min-height:10px'>";
        printf (("%s &lt;sip:%s@%s&gt;"),$this->fullName,$this->username,$this->domain);

        print "</span>
            <span class='pull-right' style='min-height:10px'>";
        if ($this->login_type == 'subscriber' && !$this->isEmbedded()) {
            print "<a href=sip_logout.phtml>";
            print _("Logout");
            print "</a>";
        } else {
            if ($this->enable_thor) {
                print " ";
                if ($this->isEmbedded()) {
                   print "<i class=\"icon-home icon-white\"></i> ";
                    print _("Home Node");
                } else {
                    print "<a href=\"http://www.ag-projects.com/SIPThor.html\" target=_new>";
                    print _("SIP Thor Node");
                    print "</a>";
                }

                if ($this->homeNode=getSipThorHomeNode($this->account,$this->sip_proxy)) {
                    printf (" <font color=green>%s</font>",$this->homeNode);
                } else {
                    print " <font color=red>";
                    print _("Unknown");
                    print "</font>";
                }
            }
        }

        print "</span></div></div>
            </div>
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

        //print_r($this->divertTargets);

    }

    function pstnChangesAllowed() {
        dprint("pstnChangesAllowed()");
        if ($this->login_type == 'subscriber') {
            $this->pstn_changes_allowed = false;
            return;
        } else {
            if ($this->login_type == 'admin') {
                $this->pstn_changes_allowed = true;
                return;
            // for a reseller we need to check if a subaccount is allowed
            } else if ($this->loginCredentials['customer'] == $this->loginCredentials['reseller']) {
                if ($this->resellerProperties['pstn_changes']) {
                    dprint("is reseller");
                    $this->pstn_changes_allowed = true;
                }
                return;
            } else if ($this->customerImpersonate == $this->loginCredentials['reseller']) {
                if ($this->resellerProperties['pstn_changes']) {
                   dprint("impersonate reseller");
                   $this->pstn_changes_allowed = true;
                }
                return;
            } else if ($this->resellerProperties['pstn_changes'] && $this->customerProperties['pstn_changes']) {
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
        } else {

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
            } else if ($this->resellerProperties['sms_access'] && $this->customerProperties['sms_access']) {
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
        } else {
            if ($this->login_type == 'admin') {
                $this->prepaid_changes_allowed = true;
                return;
            // for a reseller we need to check if a subaccount is allowed
            } else if ($this->loginCredentials['customer'] == $this->loginCredentials['reseller']) {
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
            } else if ($this->resellerProperties['prepaid_changes'] && $this->customerProperties['prepaid_changes']) {
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

        if ((new PEAR)->isError($result)) {
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
                $this->availableGroups['free-pstn'] = array(
                    "Group"      => "free-pstn",
                    "WEBName"    => sprintf(_("PSTN Access")),
                    "WEBComment" => sprintf(_("Caller-ID")),
                    "SubscriberMayEditIt" => 0,
                    "SubscriberMaySeeIt"  => 1,
                    "ResellerMayEditIt"   => 1,
                    "ResellerMaySeeIt"    => 1
                );

                if ($this->change_privacy_access_number) {
                    $_comment = sprintf(_("Dial %s to change"), $this->change_privacy_access_number);
                } else {
                    $_comment = '';
                }

                $this->availableGroups['anonymous'] = array(
                    "Group"   => "anonymous",
                    "WEBName" => sprintf (_("PSTN Privacy")),
                    "WEBComment" => $_comment,
                    "SubscriberMaySeeIt"  => 1,
                    "SubscriberMayEditIt" => 1,
                    "ResellerMayEditIt"   => 1,
                    "ResellerMaySeeIt"    => 1
                );
                if ($this->pstn_access) {
                    $this->availableGroups['rate-on-net']  = array("Group"=>"rate-on-net",
                                                    "WEBName" =>sprintf(_("Rate on net")),
                                                    "SubscriberMayEditIt"=>0,
                                                    "SubscriberMaySeeIt"=>0,
                                                    "ResellerMayEditIt"=>1,
                                                    "ResellerMaySeeIt"=>1
                                                    );

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

                if ($this->require_proof_of_identity) {
                    $this->availableGroups['payments'] = array("Group"=>"payments",
                                                    "WEBName" =>sprintf(_("CC Payments")),
                                                    "SubscriberMayEditIt"=>0,
                                                    "SubscriberMaySeeIt"=>0,
                                                    "ResellerMayEditIt"=>1,
                                                    "ResellerMaySeeIt"=>1
                                                    );
                }
            }

            return true;
        }

        $this->CustomerPort->addHeader($this->SoapAuthCustomer);
        $result     = $this->CustomerPort->getAccount(intval($this->reseller));

        if ((new PEAR)->isError($result)) {
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

        if ($this->resellerProperties['digest_settings_page']) {
            $this->digest_settings_page = $this->resellerProperties['digest_settings_page'];
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

            if ($this->change_privacy_access_number) {
                $_comment = sprintf(_("Dial %s to change"), $this->change_privacy_access_number);
            } else {
                $_comment = '';
            }

            $this->availableGroups['anonymous'] = array(
                "Group"      => "anonymous",
                "WEBName"    => sprintf (_("PSTN Privacy")),
                "WEBComment" => $_comment,
                "SubscriberMaySeeIt"  => 1,
                "SubscriberMayEditIt" => 1,
                "ResellerMayEditIt"   => 1,
                "ResellerMaySeeIt"    => 1
            );

            $this->availableGroups['rate-on-net'] = array(
                "Group"   => "rate-on-net",
                "WEBName" => sprintf(_("Rate on net")),
                "SubscriberMayEditIt" => 0,
                "SubscriberMaySeeIt"  => 0,
                "ResellerMayEditIt"   => 1,
                "ResellerMaySeeIt"    => 1
            );

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

    }

    function getDiversions() {
        dprint("getDiversions()");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getCallDiversions($this->sipId);

        if ((new PEAR)->isError($result)) {
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

            if (($uri == "<voice-mailbox>" || $uri == "voice-mailbox") && $this->absolute_voicemail_uri) {
                $uri = $this->voicemail['Account'];
            } else if ($uri == "voice-mailbox") {
                $uri = "<voice-mailbox>";
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

        if ((new PEAR)->isError($result)) {
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
        <div class='row-fluid'>
        <div class='span12'>
        ";

        print "
        </div>
        </div>
        ";

    }

    function showTabs() {
        print "<div class='pull-left'>";

        if ($this->isEmbedded()) {
            print $this->embedded_img;;
        }

        print "</div>
        <div class='pull-right'>
        <ul class=\"nav nav-tabs\">
        ";

        $items=0;

        while (list($k,$v)= each($this->tabs)) {
            if ($this->tab==$k) {
                $_class='active selected_tab';
            } else {
                $_class='tabs';
            }
            print "
                <li class=$_class><a href='$this->url&tab=$k'>$v</a></li>";
        }
        print "
        </ul>
        ";
        print "</div>";
    }

    function showUnderTabs() {
        print "
        <div class='row-fluid'>
        ";
        print "
        </div>
        ";
    }

    function addInvoice($cardProcessor) {
        // called after CC payment sucessfull
    }

    function showPaymentsTab() {
        if (!$this->show_payments_tab) {
            return false;
        }

        if ($this->login_type == 'subscriber' && in_array("blocked",$this->groups)) {
            return false;
        }

        if ($_REQUEST['task'] == 'showprices') {
            $chapter=sprintf(_("Price list"));
            $this->showChapter($chapter);

            include($this->pstn_termination_price_page);

            return true;
        }

        $chapter=sprintf(_("Payments"));
        $this->showChapter($chapter);

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

        $this->getBalanceHistory();

        $today_summary = $this->getTodayBalanceSummary();

        if ($today_summary['credit'] >= $this->max_credit_per_day) {
            print "
            <tr>
            <td colspan=3>
            ";

            if ($account->login_type!='subscriber') {
                print "<p>";
                printf ("<font color=red>Daily Credit Exceeded</font>");
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

        if (!count($this->balance_history)) {
            $this->first_transaction=true;
        } else {
            $this->first_transaction=false;
        }

        print "
        <tr>
        <td colspan=3>
        ";
        print "<p>";
        printf (_("Calling to telephone numbers is possible at the costs set forth in the <a href=%s&tab=payments&task=showprices>Price List</a>. "),$this->url);
        //printf (_("You can purchase credit with a <a href=%s&tab=payments&method=creditcard>Credit Card</a> or <a href=%s&tab=payments&method=btc>Bitcoin</a>. "),$this->url, $this->url);
        //print "<p>";
        //printf (_("You can purchase credit using <a href=http://bitcoin.org target=_new>Bitcoin</a>. "), $this->url);

        print "<br><br>";

        print "
        </td>
        </tr>
        ";

        $credit_amount = 20;
        //$method = 'btc';

        if ($method == 'btc') {
            print "<p>Select an amount and click submit to go the Bitcoin payment page.";
            printf("<form action='https://mdns.sipthor.net/bitcoin/' target=_new method='POST'>
            <input type='hidden' name='account' value='%s'>
            Amount <input class=span1 type='text' name='amount' value='20'> USD
            <p><input class=btn type=submit value='Submit'>
            </form>
            ", $this->account);
        } else {
            $chapter=sprintf(_("Credit Card"));
            $this->showChapter($chapter);
            if ($this->require_proof_of_identity) {
                if ($this->login_type == 'subscriber') {
                    if (!in_array("payments",$this->groups)) {
                        $this->showIdentityProof();
                    }
                } else {
                    $this->showIdentityProof();
                }

                if (!in_array("payments",$this->groups)) {
                    return false;
                }
            }

            $payment_processor = new $this->payment_processor_class($this);

            if ($payment_processor->fraudDetected()) {
                $chapter=sprintf(_("Payments"));
                $this->showChapter($chapter);

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

            $basket = array('pstn_credit'=>array('price'       => $credit_amount,
                                                 'description' => _('Prepaid Credit'),
                                                 'unit'        => 'credit',
                                                 'duration'    => 'N/A',
                                                 'qty'         => 1
                                                 )
                                   );

           // print "<pre>";
           // print_r($payment_processor);
           // print "</pre>";
            $payment_processor->doDirectPayment($basket);

           //print "<pre>";
            //
            //print_r($payment_processor);
            //print "</pre>";
            if ($payment_processor->transaction_results['success']) {
                    // add PSTN credit
                    $this->addBalanceReseller($credit_amount,sprintf("CC transaction %s",$payment_processor->transaction_results['id']));
            }

            if ($this->first_transaction && $payment_processor->make_credit_checks) {
                // block account temporary to check the user
                // $transaction_data= $payment_processor->['CardProcessor']['transaction_data'];
                // if ( $this->email != $transaction_data['USER_EMAIL'] ||
                //      $this->
                $this->SipPort->addHeader($this->SoapAuth);
                $result     = $this->SipPort->removeFromGroup(array("username" => $this->username,"domain"=> $this->domain),"free-pstn");
            }
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
                addslashes($this->username),
                addslashes($this->domain),
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
            addslashes($this->username),
            addslashes($this->domain)
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
        addslashes($this->username),
        addslashes($this->domain)
        );

        if (!$this->db->query($query)) {
            print "<font color=red>";
            printf ("Error for database query: %s (%s)", $this->db->Error,$this->db->Errno);
            print "</font>";
        }

        if ($this->db->num_rows()) {

            print "
                <div class=row-fluid>
                <table class='table table-condensed table-striped'>";

	    if (!in_array("payments",$this->groups)) {
                print "<p>";
                print _("Credit Card payments will be activated after your identity is verified. ");
            }

            printf ("<tr><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td>",
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
            </div>
            ";

        } else {
            print "
                <div class=row-fluid>
                ";

            print "<p>";
            print _("Credit Card payments are available only to verified customers. ");

            print "<p>";
            printf (_("To become verified, upload a copy of your passport or driving license that matches the Credit Card owner. "),$this->billing_email, $this->account, $this->billing_email);
            print "<p>";
            printf (_("This copy will be kept on your profile until the credit card transaction has been approved. "));

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
                <table class='table table-condensed table-striped'>";

            print "
            <tr>
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
            <tr>
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

            if (in_array("free-pstn",$this->groups)) {
                print "
                <tr>
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
            }

            print "
            <tr>
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
            <tr>
            <td colspan=3>
            ";
            print "
            <input type=submit value=";
            print _("Save");
            print ">
            </table>
            </div>
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
        addslashes($this->username),
        addslashes($this->domain)
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
            <div class='row-fluid'>
                <div class=span12>
                    <table class='table table-condensed table-striped'>
                        <tr>
                            <td style=\"width: 20%\">";
        print _("SIP Address");
        print "</td>
                            <td>sip:$this->account</td>
                        </tr>";
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
                        <tr>
                            <td>";
        print _("Username");
        print "</td>
                            <td>$this->username</td>
                        </tr>";
        print "
                        <tr>
                            <td>";
        print _("Domain/Realm");
        print "</td>
                            <td>$this->domain</td>
                        </tr>";

        print "
                        <tr>
                            <td>";
        print _("Outbound Proxy");
        print "</td>
                            <td>$this->sip_proxy</td>
                        </tr>
        ";

        if ($this->xcap_root) {
                print "
                    <tr>
                    <td>";
                print _("XCAP Root");
                print "
                </td>
                <td>$this->xcap_root
            </td>
            </tr>
            ";
        }
        print "</table></div></div>";


        if ($this->pstn_access && $this->rpid) {
            $chapter=sprintf(_("PSTN"));
            $this->showChapter($chapter);

            print "
                <div class=row-fluid>
                <div class=span12>
                <table class='table table-condensed table-striped'><tr><td style=\"width: 20%\">";
              print _("Caller-ID");
              print "</td>
              <td>$this->rpid</td>
            </tr>
            ";


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
                <tr>
                <td>";
            print _("Phone Number");
            print "</td>
              <td>$e</td>
            </tr>
            ";
        }

        print "</table></div></div>";
	}
        $chapter=sprintf(_("Aliases"));
        $this->showChapter($chapter);

        print "
        <div class=row-fluid>
        <div class=span12>";
        printf (_("You may create new aliases for incoming calls"));
        printf ("
            </div>
            </div>
        ");

        $t=0;

        print "
        <form class=form-horizontal method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        foreach($this->aliases as $a)  {
            $t++;

            $rr=floor($t/2);
            $mod=$t-$rr*2;

            if ($mod ==0) {
                $_class='even';
            } else {
                $_class='odd';
            }

            print "
            <div class='control-group $_class'>

              <label for=aliases[] class=control-label>";
                print _("SIP Alias");
                print "
              </label>
              <div class='controls'><input type=text size=35 name=aliases[] value=\"$a\">
              </div>
            </div>
            ";
        }

        print "
        <div class='control-ground $_class'>
          <label for=aliases[] class=control-label>";
            print _("New SIP Alias");
            print "
          </label>";
            print "
          <div class=controls>
            <input type=hidden name=action value=\"set aliases\">
        ";
         print '
             <input name=aliases[] size="35" type="text">
             </div><div class=form-actions>
             <input class="btn" type="submit" value="';
        print _("Save aliases");
        print '" onClick=saveHandler(this)>
              </div>
            </div>
           ';

        print $this->hiddenElements;

        print "
        </form>
        ";

        if (!$this->isEmbedded() && $this->show_tls_section) {

            if ($this->enrollment_url) {
                include($this->enrollment_configuration);

                if (is_array($enrollment)) {
                    $chapter=sprintf(_("TLS Certificate"));
                    $this->showChapter($chapter);
                    print "
                    <tr>
                    <td>";
                    print _("X.509 Format");
                    printf ("
                    </td>
                    <td><a href=%s&action=get_crt>Certificate</a>
                    </td>
                    </tr>
                    ",$this->url);

                    /*
                    print "
                    <tr>
                    <td>";
                    print _("PKCS#12 store format");
                    printf ("
                    </td>
                    <td><a href=%s&action=get_p12>Certificate</a>
                    </td>
                    </tr>
                    <tr>
                      <td height=3 colspan=2></td>
                    </tr>",$this->url);
                    */

                }
            }
        }

        print "
        <form method=post>";

        print "
          <div class=well>
        ";

        if ($this->email) {
            printf (_("Email SIP Account information to %s"),$this->email);
            print "
            <input type=hidden name=action value=\"send email\">
            <button class='btn btn-primary' type=submit>
            <i class=\"icon-envelope icon-white\"> </i> ";
            print _("Send");
            print "</button>";
        }

        if ($this->sip_settings_page && $this->login_type != 'subscriber') {
            print "<p>";
            printf (_("Login using SIP credentials at <a href=%s>%s</a>"),$this->sip_settings_page,$this->sip_settings_page);
            print "</p>";
        }
        print $this->hiddenElements;
        print "
        </div></form>
        ";
        if($this->sip_settings_page) {
            $this->getbalancehistory();

            if (count($this->balance_history) == "0"  || $this->login_type != 'subscriber') {
                print "<form method=post><p>";
                print "<input type=hidden name=action value=\"delete account\">";
                $date1= new datetime($this->Preferences['account_delete_request']);
                $today= new datetime('now');
                if ($this->Preferences['account_delete_request'] && $this->login_type != 'subscriber' ) {
                    print "<p>User made a deletion request on: ";
                    print $this->Preferences['account_delete_request'];
                    print "</p>";
                }

                if ($date1->diff($today)->d >= '2' || $this->Preferences['account_delete_request'] == '' || $this->login_type != 'subscriber' ) {
                    print '<button data-original-title="';
                    print _("Delete request");
                    print "\" data-trigger=\"hover\" data-toggle=\"popover button\" data-content=\"";
                    print " You may request the deletion of your account here. An email confirmation is required to validate the request.\"";
                    print " rel='popover' class='btn btn-warning' type='submit'>";
                    print _("Delete request");
                } else {
                    //print "<button rel='popover' class='btn btn-disabled'disabled type='submit'>";
                    //printf (_("Account remove request is active"));
                    print "A deletion request has been made on: ";
                    print $this->Preferences['account_delete_request'];
                }
                print "</button></p>";
            }

            print $this->hiddenElements;

            print "
            </form>
            ";
        }
    }

    function showDownloadTab() {

        $chapter=sprintf(_("SIP Client download"));
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

    function showDIDTab() {
        if (class_exists($this->did_processor_class)) {
            $did_processor = new $this->did_processor_class();
        }

        if (!$_REQUEST['ddi_action']) {
            $chapter=sprintf(_("Registered Numbers"));
            $this->showChapter($chapter);

            print "
            <tr class=odd>
            <td colspan=2>";


            $numbers=$did_processor->getOrders($this->account);

            if (count($numbers)) {
                print "<table border=0>";
                printf ("<tr bgcolor=lightgray><td>Number</td><td>Country</td><td>Expire Date</td><td>Order</td><td>Action</td></tr>");

                foreach (array_keys($numbers) as $_number) {
                    $t++;

                    $rr=floor($t/2);
                    $mod=$t-$rr*2;

                    if ($mod ==0) {
                        $_class='odd';
                    } else {
                        $_class='even';
                    }

                    $form=sprintf("

                    <select name=period>");
                    $form.=sprintf ("<option value=1>1 %s",_("Month"));
                    $form.=sprintf ("<option value=3>3 %s",_("Months"));
                    $form.=sprintf ("<option value=6>6 %s",_("Months"));
                    $form.=sprintf ("<option value=12>12 %s",_("Months"));
                    $form.=sprintf ("<option value=24>24 %s",_("Months"));
                    $form.="</select>";

                    $form.=$this->hiddenElements;

                    $form.=sprintf ("<input type=hidden name='number' value='%s'>",$_number);
                    $form.=sprintf ("<input type=submit name='ddi_action' value='Renew'>");
                    $form.=sprintf ("<input type=submit name='ddi_action' value='Drop'>");

                    printf ("<tr class=$_class><td valign=top>+%s</td><td valign=top>%s</td><td valign=top>%s</td><td valign=top>%s</td><form method=post><td>%s</td></form></tr>",$_number,$numbers[$_number]['country_name'],$numbers[$_number]['did_expire_date_gmt'],$numbers[$_number]['order_id'],$form);
                }
                print "</table>";
            }

            print "
            </td>
            </tr>
            ";

        }

		if ($prefixes = $did_processor->getPrefixes()) {
            if ($_REQUEST['ddi_action'] == 'register' && $_REQUEST['prefix'] && $_REQUEST['period']) {

                $chapter=sprintf(_("Register New Number"));
                $this->showChapter($chapter);

                print "
                <tr class=odd>
                <td colspan=2>";

                $total=$prefixes[$_REQUEST['prefix']]['setup']+$prefixes[$_REQUEST['prefix']]['monthly']* $_REQUEST['period'];

                $basket = array('ddi_number' => array('price'       => sprintf("%.2f",$total),
                                                      'description' => sprintf(_('Telephone Number (+%s %s) for %d months'),$_REQUEST['prefix'],$prefixes[$_REQUEST['prefix']]['country_name'],$_REQUEST['period']),
                                                      'unit'        => 'number',
                                                      'duration'    => 'N/A',
                                                      'qty'         => 1
                                                      )
                                       );

                $this->hiddenElements=sprintf("
                <input type=hidden name=prefix value='%s'>
                <input type=hidden name=period value='%s'>
                <input type=hidden name=ddi_action value='register'>
                ",
                $_REQUEST['prefix'],
                $_REQUEST['period']
                );

                $data=array('customer_id'   => $this->owner,
                            'country_iso'   => $prefixes[$_REQUEST['prefix']]['country_iso'],
                            'city_prefix'   => $prefixes[$_REQUEST['prefix']]['city_prefix'],
                            'period'        => $_REQUEST['period'],
                            'map_data'      => array(
                                                     'map_type'        => 'URI',
                                                     'map_proto'       => 'SIP',
                                                     'map_detail'      => $this->account,
                                                     'map_pref_server' => 1
                                                    ),
                            'prepaid_funds' => "0",
                            'uniq_hash'     => md5(mt_rand())
                            );

            	$did_processor->createOrder($data);

                /*
                if (class_exists($this->payment_processor_class)) {
                    $payment_processor = new $this->payment_processor_class($this,$basket);
                }

                if ($payment_processor->transaction_results['success']) {
                	if ($did_processor->createOrder($data)) {
                        // add ENUM entry
                    } else {
                        // notify admin about payment without service fullfilment
                    }
                }
                */
                print "
                </td>
                </tr>
                ";

            } else if ($_REQUEST['ddi_action'] == 'Renew' && $_REQUEST['number'] && $_REQUEST['period']) {
                $chapter=sprintf(_("Renew Number"));
                $this->showChapter($chapter);

                print "
                <tr class=odd>
                <td colspan=2>";
                $data=array('customer_id'   => $this->owner,
                            'did_number'    => $_REQUEST['number'],
                            'period'        => $_REQUEST['period'],
                            'uniq_hash'     => md5(mt_rand())
                            );

                print "Renewing number....";
            	$did_processor->renewOrder($data);

                print "
                </td>
                </tr>
                ";

            } else if ($_REQUEST['ddi_action'] == 'Drop' && $_REQUEST['number']) {
            	$chapter=sprintf(_("Cancel Number"));
                $this->showChapter($chapter);

                print "
                <tr class=odd>
                <td colspan=2>";
                $data=array('customer_id'   => $this->owner,
                            'did_number'    => $_REQUEST['number']
                            );

            	$did_processor->cancelOrder($data);

                print "
                </td>
                </tr>
                ";

            } else {
            	$chapter=sprintf(_("Register New Number"));
                $this->showChapter($chapter);

                print "
                <tr class=odd>
                <td colspan=2>";
                print "
                <form method=post>";

                print _("Select a region where you want to have a telephone number: ");
                print "<p>";


                print "<select name=prefix>";

                foreach (array_keys($prefixes) as $prefix) {

                	if (!$found_country && $this->owner_information['country'] == $prefixes[$prefix]['country_iso']) {
                        $selected='selected';
                        $found_country=true;
                    } else {
                        $selected='';
                    }

                    if ($prefixes[$prefix]['setup']) {
                        printf ("<option value='%s' %s>%s %s (+%s) - Setup %s USD, Monthy %s USD",$prefix,$selected,$prefixes[$prefix]['country_name'],$prefixes[$prefix]['city_name'],$prefix,$prefixes[$prefix]['setup'],$prefixes[$prefix]['monthly']);
                    } else {
                        printf ("<option value='%s' %s>%s %s (+%s) - Monthy %s USD",$prefix,$selected,$prefixes[$prefix]['country_name'],$prefixes[$prefix]['city_name'],$prefix,$prefixes[$prefix]['monthly']);
                    }
                }

                print "</select>";

                print "<p>";
                print _("Select the duration for which you want to use the telephone number: ");

                print "<p>";
                print "<select name=period>";
                printf ("<option value=1>1 %s",_("Month"));
                printf ("<option value=3>3 %s",_("Months"));
                printf ("<option value=6>6 %s",_("Months"));
                printf ("<option value=12>12 %s",_("Months"));
                printf ("<option value=24>24 %s",_("Months"));
                print "</select>";

                print $this->hiddenElements;

                print "<p>";
                print "<input type=hidden name='ddi_action' value='register'>";
                print "<input type=submit value='Purchase'>";
                print "
                </form>
                ";
            }

            print "
            </td>
            </tr>
            ";

        } else {
            print "<p><font color=red>Error fetching DDI prefixes</font>";
        }

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

        if ($this->create_certificate) {
            if ($_passport = $this->generateCertificate()) {
                    $_account['passport']       = $_passport;
            }
        }

        $_account['sip_address']    = $this->account;
        $_account['display_name']   = sprintf("%s %s",$this->firstName,$this->lastName);
        $_account['password']       = $this->password;
        $_account['web_password']   = $this->Preferences['web_password'];
        $_account['email']          = $this->email;
        $_account['settings_url']   = $this->digest_settings_page;
        $_account['xcap_root']      = $this->xcap_root;
        $_account['outbound_proxy'] = $this->sip_proxy;

        if ($this->enrollment_url) {
            include($this->enrollment_configuration);
            if (is_array($enrollment)) {
                $_account['msrp_relay']        = $enrollment['msrp_relay'];
                $_account['conference_server'] = $enrollment['conference_server'];
                $_account['settings_url']      = $enrollment['settings_url'];
                if ($enrollment['ldap_hostname']) {
                    $_account['ldap_hostname']     = $enrollment['ldap_hostname'];
                }
                if ($enrollment['ldap_dn']) {
                    $_account['ldap_dn']           = $enrollment['ldap_dn'];
                }
            }
        }

        if ($this->store_clear_text_passwords=='false') {
                $match_password=false;

                if ($_REQUEST['password']) {
                    $str=$this->result->id->username.":".$this->result->id->domain.":".$_REQUEST['password'];
                    if (md5($str) == $this->result->ha1) {
                        $_account['password']=$_REQUEST['password'];
                        $match_password=true;
                    }
                }

                if (!$match_password){
                    print "<form method='POST' id='password_download' class='form-horizontal' action='$this->url'><p>";
                    print _("Please enter your SIP account password: ");
                    if ($_REQUEST['password'] || $_REQUEST['continue']) {
                        print "</p><div id='pass_group' class='control-group error'>";
                    } else {
                        print "</p><div id='pass_group' class=control-group>";
                    }
                    print "<label class=control-label>";
                    print _("Password");
                    print "</label>";
                    print "<div id='controls_password' class=controls>";
                    print "<input class='input' type='password' id='password' name='password' placeholder='";
                    print _("Enter your password");
                    print "'' value='";
                    print $_REQUEST['password'];
                    print "'>";
                    if ($_REQUEST['password'] || $_REQUEST['continue']) {
                        print "<span id=\"help-text\" class=\"help-inline\">Entered password does not match your account</span>";
                    }
                    print "</div></div>";
                    print "<input type='hidden' name='tab' value='download'>";
                    print "<div class='form-actions'>";
                    print "<input type=submit value='Continue' class='btn btn-primary'>";
                    print "<input type='hidden' name='continue' value='1'>";
                    print "</div></form>";
                    $class='hide';
                }
        }

        print "<div class=$class id='java_buttons'><table border=0>";

        if (in_array($os,$this->valid_os)) {
            print "<tr><td>";

            printf (_("Download and install <a href=%s target=blink>%s</a> preconfigured with your SIP account:"), $this->blink_download_url, $this->show_download_tab);
            print "</td></tr>";
            print "<tr><td>";
            printf ("<applet code='com.agprojects.apps.browserinfo.BlinkConfigure' archive='blink_download.jar?version=%s' name='BlinkDownload' height='35' width='250' align='left'>
            <param name='label_text' value='Download'>
            <param name='click_label_text' value='Downloading...'>
            <param name='download_url' value='%s'>
            <param name='file_name' value=''>
            <param name='file_content' value='%s'>
            </applet>",
            rand(),
            $this->blink_download_url,
            rawurlencode(json_encode($_account))
            );
        	print "</td></tr>";

        } else {
        	print "<tr><td>";

            print "<p>";
            printf (_("To download %s visit <a href='%s' target=blink>%s</a>"),$this->show_download_tab, $this->blink_download_url, $this->blink_download_url);
        	print "</td></tr>";
        }

        print "<tr><td>";

        printf (_("If you have already installed %s, you can configure it to use your SIP account:"), $this->show_download_tab);

        print "</td></tr>";
        print "<tr><td>";

        printf ("<applet code='com.agprojects.apps.browserinfo.BlinkConfigure' archive='blink_download.jar?version=%s' name='BlinkConfigure' height='35' width='250' align='left'>
        <param name='label_text' value='Configure this account'>
        <param name='click_label_text' value='Please restart %s now!'>
        <param name='download_url' value=''>
        <param name='file_name' value=''>
        <param name='file_content' value='%s'>
        </applet>",
        rand(),
        $this->show_download_tab,
        urlencode(json_encode($_account))
        );

        print "</td></tr>";
        print "</table>";

        print "<p>";
        printf ("Notes. ");
        print _("<a href='http://www.java.com/en/download/manual.jsp'>Java Runtime Environment</a> (JRE) must be activated in the web browser. ");
        print "</div>";
    }

    function showFooter() {
        print "
          <div class='pull-right'>";

        if ($this->footerFile) {
            include ("$this->footerFile");
        } else {
            print "<a href=http://ag-projects.com target=agprojects><img src=images/PoweredbyAGProjects.png border=0></a>";
        }

        print "</div>
        ";

    }

    function showSettingsTab() {

        $use_yubikey=0;
        if (stream_resolve_include_path('Auth/Yubico.php')) {
            require_once 'Auth/Yubico.php';
            $use_yubikey=1;
        }

        $this->getVoicemail();

        print "
        <form class=form-horizontal method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        $chapter=sprintf(_("SIP Account"));
        $this->showChapter($chapter);

        if ($this->login_type != "subscriber" ) {

            print "
            <div class='control-group even'>
            <label class='control-label' for='first_name'>";
            print _("First Name");
            print "
            </label>";

            print "<div class=\"controls\"><input class=input-medium type=text size=15 name=first_name value=\"$this->firstName\">";

            print "
            </div>
            </div>
            ";

            print "
            <div class='control-group odd'>
            <label class='control-label' for='last_name'>";
            print _("Last Name");
            print "</label>
            <div class='controls'>";

            print "<input class=input-medium type=text size=15 name=last_name value=\"$this->lastName\">";

            print "
            </div>
            </div>
            ";

        }

        print "
        <div class='control-group even'>
        <label class='control-label' for='sip_password'>";
        print _("Password");

        print "
        </label>
        <div class='controls'>";
        if ($this->login_type == 'subscriber' && in_array("deny-password-change",$this->groups)) {
            print _("Password can be changed only by the operator");
        } else {
            print '<input class=input-medium type=password size=15 name=sip_password rel="popover" title="" data-original-title="';
            print _("Password");
            print "\" data-trigger=\"focus\" data-toggle=\"popover\" data-content=\"";
            print _("Enter text to change the current password");
            print "\">";
            printf ("\n\n<!-- \nSIP Account password: %s\n -->\n\n",$this->password);
        }

        print "</span>
        </div>
        </div>
        ";

        print "
        <div class='control-group odd'>
        <label class='control-label' for='web_password'>";
        print _("Web Password");
        print "
        </label>
        <div class='controls'><div>";

        print '<input class=input-medium type=password size=15 name=web_password  rel="popover" title="" data-original-title="';
        print _("Web Password");
        print "\" data-trigger=\"focus\" data-toggle=\"popover button\" data-content=\"";
        print _("Enter text to change the password to access this web page");
        print "\">";
//        print '<span class=help-inline>';
        print ' <label class="checkbox inline">';
        print '<input type="checkbox" name="web_password_reset" value="1"> ';
        print 'Remove web password';
        print '</label>';
        //print _("Enter text to change the password to access this web page");

        print "
        </div></div>
        </div>
        ";

        if ($use_yubikey == 1 && !$this->isEmbedded()) {
            print "
            <div class='control-group odd'>
            <label class='control-label' for='yubykey'>";
            print _("Yubikey");
            print "
            </label>
            <div class=controls>";

            print "<input class=input-medium type=text size=12 maxlength=12 rel='popover' title=\"\" data-original-title='";
            print _("Yubikey");
            print "' data-trigger=\"focus\" data-toggle=\"popover\" data-content=\"";
            print _("Enter <strong>Yubikey id</strong> to allow SIP Account + Yubikey login to access this webpage.<br /><br/>The Yubikey id is the first 12 digits of the string generated by the key.<br /><br/>It can be set by clicking in this text field and pressing your Yubikey.");
            printf ("\"name=yubikey value=\"%s\"><span class=help-inline>",$this->Preferences['yubikey']) ;

            print _("Enter Yubikey id");

            print "</span>
            </div>
            </div>
            ";
            //print '<pre>';
            //print($this->Preferences['yubikey']);
            //print_r($this);
            //print '</pre>';
        }

        print "
        <div class='control-group even'>
        <label class='control-label' for='language'>";
        print _("Language");
        print "
        </label>
        <div class=controls>";

        print "
        <select class=input-medium name=language>
        ";

        $selected_lang[$this->Preferences['language']]="selected";

        foreach (array_keys($this->languages) as $_lang) {
             printf ("<option value='%s' %s>%s\n",$_lang,$selected_lang[$_lang],$this->languages[$_lang]['name']);
        }

        print "
            </select>
            </div></div>
        ";

        print "
        <div class='control-group odd'>
        <label class='control-label' for='timezone'>";
        print _("Timezone");
        print "
        </label>
        <div class=controls>
        ";
        $this->showTimezones('timezone',$this->timezone);
        print " ";
        $timestamp=time();
        $LocalTime=getLocalTime($this->timezone,$timestamp);
        print "<span class=help-inline>";
        print _("Local Time");
        print ": $LocalTime";
        //dprint_r($this->availableGroups);
        print "</span>
            </div>
        </div>
        ";
        if (count($this->emergency_regions) > 0) {
            print "
            <div class='control-group'>
            <label class='control-label' for='region'>";
            print _("Location");
            print "
            </label>
            <div class=controls>
            ";
            print "<select name=region>";
            $selected_region[$this->region]="selected";
            foreach (array_keys($this->emergency_regions) as $_region) {
                printf ("<option value=\"%s\" %s>%s",$_region,$selected_region[$_region],$this->emergency_regions[$_region]);
            }
            print "</select>";

            print "
            </div>
            </div>
            ";
        }


        if ($this->pstn_access) {
            if (in_array("free-pstn",$this->groups)) {

                if (in_array("quota",$this->groups)) {
                    $_class="alert alert-error";
                } else {
                    $_class="";
                }

                if ($this->pstn_changes_allowed) {
                    print "
                        <div class='control-group'>
                        <div class='$_class'>
                        <label class=control-label>";
                    print _("Quota");
                    print "
                    </label>
                    <div class='controls'><div class='input-prepend'>";

                    printf ("<span class='add-on'>%s</span><input class=input-medium type=text size=6 maxsize=6 name=quota value='%s'></div><span class='help-inline muted'>",$this->currency,$this->quota);
                    //print "<div class=span10>";
                    if ($this->quota || in_array("quota",$this->groups)) {
                        $this->getCallStatistics();
                        if ($this->thisMonth['price']) {
                            printf (_("This month usage: %.2f %s"),$this->thisMonth['price'], $this->currency);
                            printf (" / %d ",$this->thisMonth['calls']);
                            print _("Calls ");
                        }
                    }

                    print "</span> ";


                    if ($this->pstn_changes_allowed) {
                        print "<label class='checkbox inline'>";
                        print "
                            <input type=checkbox name=quota_deblock value=1> ";
                        print _("Un-block");
                        print "</label>";
                    }

                    print "</div></div>
                    </div>
                    ";
                } else if ($this->quota) {
                    print "
                    <div class='control-group'>
                        <label class=control-label>";
                    print _("Quota");
                    print "
                        </label>
                    <div class='controls $_class'>
                        <span style='padding-top:5px; margin-bottom:5px;display:block;'>
                    ";
                    printf ("%s %s ",$this->quota,$this->currency);
                    $this->getCallStatistics();
                    if ($this->thisMonth['price']) {
                        printf (_("This month usage: %.2f %s"),$this->thisMonth['price'], $this->currency);
                        printf (" / %d ",$this->thisMonth['calls']);
                        print _("Calls");
                    }

                    print "</span></div>
                    </div>
                    ";

                }
            }

            if ($this->prepaid) $checked_box_prepaid="checked";

            if (!$this->prepaid_changes_allowed) $disabled_box_prepaid   = "disabled=true";

            print "
            <div class='control-group'>
            <label class=control-label for=prepaid>";
            print _("Prepaid");
            print "</label>
                <div class=controls>
                <label class=checkbox>
                    <input type=checkbox value=1 name=prepaid $checked_box_prepaid $disabled_box_prepaid>
                </label>
            </div>
            </div>
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
                $_class="alert alert-error";
                $_class1="error";
            } else {
                $_class="";
                $_class1='';
            }

            print "
                <div class='control-group $_class1'>
            <label class=control-label>$elementName</label>
            <div class='controls $_class'>
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

                if ($this->pstn_changes_allowed) {
                    print "<label class='checkbox inline' style=\"padding-top: 1px; line-height:14px\">
                        <input type='checkbox' value=1 class='inline' name=$key $checked_box[$key] $disabled_box>
                    ";
                    print "$elementComment";
                    print "</label> <input id='rpid_input'style='display:inline-block' class=input-medium type=text size=15 maxsize=15 name=rpid value=\"$this->rpid\">";
                } else {
                    print "<label class='checkbox inline' style=\"padding-top: 5px; line-height:14px\">
                        <input type='checkbox' value=1 class='inline' name=$key $checked_box[$key] $disabled_box>
                    ";
                    if ($this->rpid) {
                        print "$elementComment: $this->rpid </label>";
                    } else {
                        print "$elementComment</label>";
                    }
                }

            } else {
                print "
                <label class=checkbox><input type=checkbox value=1 name=$key $checked_box[$key] $disabled_box>$elementComment</label>
                ";
            }

            print "
            </div>
            </div>
            ";

        }

        $this->showExtraGroups();

        $this->showOwner();

        $this->showQuickDial();

        $this->showMobileNumber();
        $this->showIPAccessList();
        $this->showCallLimit();

        print "
        <div class='control-group even'>
        <label for=timeout class=control-label>";
        print _("No-answer Timeout");
        printf ("
        </label>
        <div class='controls'>
        <div class='input-append'>
        ");

        print "<input rel='popover' class=input-medium name=timeout title=\"\" data-original-title='";
        print _("No-answer Timeout");
        print "' data-trigger=\"focus\" data-toggle=\"popover\" data-content=\"";
        print _("Used to determined after how many seconds the Forwarding action for this condition will occur");
        printf ("\" value='%d' size=3 type=number max=\"900\"><span class='add-on'>s</span>",$this->timeout);

        print "
        </div>
        </div>
        </div>
        ";

        print "
        <div class='row-fluid odd'>
        <label for=extra class=control-label>";
        print _("Tabs");
        print "
        </label>
        <div id='extra' class=controls>";

        if (in_array("free-pstn",$this->groups) && !$this->show_barring_tab) {
            if ($this->Preferences['show_barring_tab']){
                $check_show_barring_tab="checked";
            } else {
                $check_show_barring_tab="";
            }
            printf ("<label class='checkbox'><input type=checkbox %s value=1 name='show_barring_tab'>%s</label>\n",$check_show_barring_tab,_("Barring"));
        }

        print "
        </div>
        </div>
        ";

        $this->showVoicemail();

        $this->showBillingProfiles();

        $chapter=sprintf(_("Notifications Address"));
        $this->showChapter($chapter);

        print "
        <div class='control-group even'>
          <label for=mailto class=control-label>";
            print _("Email Address");
            print "
          </label>
          <div class=controls>
            <input class=span3 type=text size=40 maxsize=255 name=mailto value=\"$this->email\">
          </div>
        </div>
        ";

        print "
            <input type=hidden name=action value=\"save settings\">
        ";

        print "<div class='form-actions'>
        <input class='btn' type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";
        print "
          </div>
        ";

        print $this->hiddenElements;

        print "
        </form>
        ";

    }

    function showDiversionsTab () {

        $this->getVoicemail();
        $this->getEnumMappings();
        $this->getDivertTargets();
        $this->getDiversions();

        print "
        <form class=form-horizontal method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        $chapter=sprintf(_("Call Forwarding"));
        $this->showChapter($chapter);

        $this->showDiversions();

        print "
          <div class=form-actions>
            <input type=hidden name=action value=\"set diversions\">
        ";

        print "
        <input class='btn' type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";
        print "
          </div>
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
        <div class='control-group even'>
        <label for=voicemail class=control-label>";
        print _("Enable");
        print "</label>
        <div class=controls>
        <label class=checkbox>
        <input type=checkbox value=1 name=voicemail $checked_voicemail $disabled_box>";


        if ($this->voicemail['Account'] &&
            ($this->login_type != 'subscriber')) {
            print " (";
            print _("Mailbox");
            printf (" %s) ",$this->voicemail['Account']);
        }

        print "</label>
            </div>
        </div>
        ";

        if ($this->voicemail['Account']) {

            print "
            <div class='control-group odd'>
            <label for=delete_voicemail class=control-label>";

            print _("Delivery");
            print "</label>
            <div class='controls'>
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
                print "<select class=span6 name=delete_voicemail>";
                $_text=sprintf(_("Send voice messages by e-mail to %s"),$this->email);
                printf ("<option value=1 %s>%s",$selected_store_voicemail['email'],$_text);
                printf ("<option value=0 %s>%s",$selected_store_voicemail['server'],_("Send messages by e-mail and store messages on the server"));
                print "</select>";
            } else {
                printf (_("Voice messages are sent by email to %s"),$this->email);
            }

            print "
            </div>
            </div>
            ";

            if (!$this->voicemail['DisableOptions']) {

                print "
                <div class='control-group even'>
                <label for=voicemail_password class=control-label>";

                print _("Password");
                print "</label>
                <div class=controls>
                ";

                printf ("<input class=input-medium type=text size=15 name=voicemail_password value=\"%s\">",$this->voicemail['Password']);

                print "
                </div>
                </div>
                ";

                if ($this->voicemail_access_number) {
                   print "
                       <div class=row-fluid>
                       <div class=span1></div>
                       <div class='offset1 span10'>
                       <div class=\"alert alert-info\">";
                   printf(_("Dial %s to listen to your messages or change preferences. "),$this->voicemail_access_number);
                   print "</div></div>
                   </div>
                   ";
                }
            }
        }
    }

    function showOwner() {
        if ($this->login_type == 'subscriber') {
          //print "<input type=hidden name=owner value=\"$this->owner\">";
          return true;
        }

        print "
        <div class='control-group'>
        <label for=owner class=control-label>";
        print _("Owner");
        print "</label>
        <div class=controls>
        <input class=input-medium type=text name=owner size=7  rel='popover' title data-original-title='";
        print _("Owner");
        print "' data-trigger=\"focus\" data-toggle=\"popover\" data-content=\"";
        print _("Used to link the SIP account to the customer details stored in another database like the platform customer database. Only integer values are allowed.");
        print "\" value=\"$this->owner\">";
            print "
            </div>
            </div>
            ";
    }

    function showDevicesTab() {
        $this->getDeviceLocations();

        if (count($this->locations)) {
            $chapter=sprintf(_("SIP Devices"));
            $this->showChapter($chapter);

            $j=0;

            print "
                <div class=row-fluid>
                <table class='table table-condensed table-striped'>";
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

                print "<tr><td align=center>";
                printf ("<img src='%s/30/%s' border=0>",$this->SipUAImagesPath,$UAImage);
                print "</td>";

                print "<td>";
                print "<i>$user_agent</i>";
                if ($transport == 'tls') print "&nbsp;<i class='icon-lock'></i>";
                print "<br><span class='label label-info'>";
                print _("Location");
                print "</span>";
                print " ";
                if (strlen($transport)) print "$transport:";
                print "$contact ";

                if ($publicContact != $contact) {
                    print " ($publicContact) ";
                }

                if ($publicContact) {
                    $_els=explode(":",$publicContact);
                    if ($_loc=geoip_record_by_name($_els[0])) {
                        $this->geo_location=$_loc['country_name'].'/'.utf8_encode($_loc['city']);
                    } else if ($_loc=geoip_country_name_by_name($_els[0])) {
                        $this->geo_location=$_loc;
                    } else {
                        $this->geo_location='';
                    }
                    printf ("%s",$this->geo_location);
                }

                print "</td><td>$expires</td>";
                print "</tr>";

            }
            print "</table></div>";
        }
    }

    function getBarringPrefixes() {
        dprint("getBarringPrefixes()");

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getBarringPrefixes($this->sipId);

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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
        <form class=form-horizontal method=post name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        print "
        <div class=row-fluid>
        <div class=span12>";
        print _("You can deny outbound calls to unwanted PSTN prefixes. ");
        print "</div></div>";

        print "
        <div class=control-group>
        <label class=control-label for=barring_prefixes[]>";
        print _("Destination Prefix");
        print "</label>";
        print "<div class=controls>
        <input type=text name=barring_prefixes[]>
        ";
        print "<span class=help-inline>";
        print _("Example");
        print ": +31900";
        print "
        </span>
        </div></div>
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
                <div class='control-group $_class'>";

                print "<label for=barring_prefixes[] class=control-label>";
                print _("Destination Prefix");
                print "</label>";
                print "<div class=controls>
                <input type=text name=barring_prefixes[] value=\"$_prefix\">
                </div>";
                print "</div>";
            }
        }

        print "
            <input type=hidden name=action value=\"set barring\">
        ";

        print "<div class=form-actions>
        <input class=btn type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";

        print "</div>
          </div>
          </div>
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
        $this->changedFields=array();
        $this->sendCEmail=0;

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
            $this->sendCEmail=1;
            array_push($this->changedFields,"Email Address");
        }

        if ($this->login_type != "subscriber") {
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

            if ($this->CallLimitChangePolicy()) {
                if ($this->soapEngines[$this->sip_engine]['call_limit']) {
                    if (isset($callLimit) && $this->callLimit != $callLimit) {
                        $result->callLimit=intval($callLimit);
                        $this->somethingChanged=1;
                    }
                }
            }
        }

        $owner=intval($owner);
        if ($owner != $this->owner  && $this->login_type != 'subscriber') {
            dprint ("change the owner");
            $result->owner=$owner;
            $this->somethingChanged=1;
        } else {
            $result->owner=$this->owner;
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

        if ($show_barring_tab != $this->Preferences['show_barring_tab'] ) {
            $this->setPreference("show_barring_tab",$show_barring_tab);
            $this->somethingChanged=1;
        }

        if ($this->Preferences['account_delete_request'] ) {
            $this->setPreference("account_delete_request",date('m/d/Y h:i:s a', time()));
            $this->somethingChanged=1;
        }

        if ($this->login_type == 'subscriber' && in_array("deny-password-change",$this->groups)) {
        } else if ($sip_password) {
        	if ($this->store_clear_text_passwords) {
            	$result->password=$sip_password;
            } else {
                $md1=strtolower($this->username).':'.strtolower($this->domain).':'.$sip_password;
                $md2=strtolower($this->username).'@'.strtolower($this->domain).':'.strtolower($this->domain).':'.$sip_password;
                $result->password=md5($md1).':'.md5($md2);
            }
            $this->sendCEmail=1;
            array_push($this->changedFields,"Password");
            $this->somethingChanged=1;
        }

        if ($web_password) {
            if ($this->store_clear_text_passwords) {
                $web_password_new=$web_password;
            } else {
                $md1=strtolower($this->username).':'.strtolower($this->domain).':'.$web_password;
                $md2=strtolower($this->username).'@'.strtolower($this->domain).':'.strtolower($this->domain).':'.$web_password;
                $web_password_new=md5($md1).':'.md5($md2);
            }
            $this->setPreference('web_password',$web_password_new);
            $this->sendCEmail=1;
            array_push($this->changedFields,"Web password");
            $this->somethingChanged=1;
        }

        if ($web_password_reset) {
            $this->setPreference('web_password','remove');
            $this->somethingChanged=1;
        }

        if ($this->Preferences['yubikey'] != $yubikey && !$this->isEmbedded()) {
            $this->setPreference('yubikey',$yubikey);
            $this->somethingChanged=1;
        }
        
        if (is_array($result->acl) and count($result->acl)) {
            foreach (array_keys($result->acl) as $key) {
                if (isset($result->acl[$key]->tag) && $result->acl[$key]->tag == '') {
                    unset($result->acl[$key]->tag);
                }
            }
        }

        if ($this->IPAccessListChangePolicy()) {
            if (isset($ip_access_list) and $this->ip_access_list != $ip_access_list) {
                $ip_access_list=preg_replace("/\s+/","\n", trim($ip_access_list));
                $list=explode("\n", trim($ip_access_list));
                $ip_access_list=array();
                foreach ($list as $el) {
                    list($ip,$mask) = explode("/",$el);
                    if ($mask <0 or $mask > 32) {
                        continue;
                    }
                    if (!preg_match("/\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}/",$ip)) {
                        continue;
                    }
                    $ip_access_list[]=array('ip'=>$ip, 'mask'=>intval($mask));
                }
                $result->acl=$ip_access_list;
                $this->somethingChanged=1;
            }
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
            $result->quickdialPrefix=$quickdial;
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
            $result->timeout=intval($timeout);
        }

        if ($result->owner == '') {
            $this->somethingChanged=1;
            $result->owner=0;
        }

        if ($result->callLimit == '') {
            $this->somethingChanged=1;
            $result->callLimit=0;
        }

        if ($result->quota == '') {
            $this->somethingChanged=1;
            $result->quota=0;
        }

        if ($result->timeout == '')  {
            $this->somethingChanged=1;
            $result->timeout=35;
        }

        if ($result->timeout > 900)  {
            $this->somethingChanged=1;
            $result->timeout=900;
        }

        if ($this->somethingChanged) {

        	$result->properties=$this->properties;

        	if (!$result->quota) $result->quota=0;

            //dprint_r($result);

            $this->SipPort->addHeader($this->SoapAuth);
            $result     = $this->SipPort->updateAccount($result);

            if ((new PEAR)->isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                return false;
            } else {
                dprint("Call updateAccount");
                if ($this->sendCEmail && $this->notify_on_sip_account_changes) {
                    $this->sendChangedEmail(False,$this->changedFields);
                }
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

            if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

            if ((new PEAR)->isError($result)) {
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

            if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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
                    if ($value != 'remove') {
                        $newProperties[]=array('name'=> $name,
                            'value' => $value);
                    }
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
        if ($this->login_type == 'subscriber' && in_array("blocked",$this->groups)) {
            return false;
        }

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
                if ($_REQUEST['task'] == 'change_balance') {
                    $description = $_REQUEST['description'];
                    $value       = $_REQUEST['value'];

                    if (strlen($value) && $result = $this->addBalanceReseller($value,$description)) {
                        print "<p><font color=green>";
                        printf (_("Old balance was %s, new balance is %s. "),$result->old_balance, $result->new_balance);
                        print "</font>";
                        $_done=true;
                    }
                } else if ($_REQUEST['task'] == 'refund') {
                    $transaction = json_decode(base64_decode($_REQUEST['transaction']));
                    printf ("Refunding transaction id %s in value of %s", $transaction->id, $transaction->value);

                    require('cc_processor.php');
                    $ccp = new CreditCardProcessor();
                    $refund_results = $ccp->refundPayment($transaction->id);

                    if(count($refund_results['error']) > 0 ){
                        printf ("<p><font color=red>Error %d: %s (%s)</font>",$refund_results['error']['error_code'], $refund_results['error']['desc'], $refund_results['error']['short_message']);
                    } else {
                        printf ("<p>Transaction %s refunded with %s: %s",$transaction->id, $refund_results['success']['desc']->RefundTransactionID,$refund_results['success']['desc']->GrossRefundAmount->_value);
                        $description=sprintf("Refund %s with %s",$transaction->id, $refund_results['success']['desc']->RefundTransactionID);
                        if ($result = $this->addBalanceReseller(-$transaction->value,$description)) {
                            print "<p><font color=green>";
                            printf (_("Old balance was %s, new balance is %s. "),$result->old_balance, $result->new_balance);
                            print "</font>";
                            $_done=true;
                        }
                    }
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
            <div class=row-fluid>
            <div class=span12>";
            print _("Your current balance is");
            print ": ";
            printf ("%.2f %s ",$this->prepaidAccount->balance,$this->currency);
            print "</div>
            </div>
            ";

            $this->showChangeBalanceReseller();
            $this->showChangeBalanceSubscriber();
            $this->showBalanceHistory();

        }
    }

    function showChangeBalanceReseller () {
        if (!$this->prepaid_changes_allowed) return false;

        $chapter=sprintf(_("Add Balance"));
        $this->showChapter($chapter);

        print "
        <form class=form-inline action=$this->url method=post>
        <input type=hidden name=tab value=credit>
        <input type=hidden name=issuer value=reseller>
        <input type=hidden name=task value=change_balance>
        <div class=control-group>
        <div class=control>";

        print "
        <input type=text size=10 name=value placeholder=\"";
        print _("Value");
        print "\">";
        print "<input type=text size=30 name=description placeholder=\"";
        print _("Description");
        print "\">";
        print " <label class=checkbox>";
        print "<input type=checkbox name=notify value=1>";
        print _("Notify");
        print "</label>
            ";
        print "<input class=btn type=submit value=";
        print _("Add");
        print ">
            </div>
            </div>
        </form>
        ";

        $transactions = $this->getPaymentIds();

        if (count($transactions)) {
            $chapter=sprintf(_("Refund Transaction"));
            $this->showChapter($chapter);

            print "
            <form class='form-inline' action=$this->url method=post>
            <input type=hidden name=tab value=credit>
            <input type=hidden name=issuer value=reseller>
            <input type=hidden name=task value=refund>
            ";

            print _("Transaction Id");

            print "
            <select class=span2 name=transaction> ";
            foreach (array_keys($transactions) as $tran) {
                $t=array('id' => $tran, 'value' => $transactions[$tran]);
                printf ("<option value='%s'>%s",base64_encode(json_encode($t)),$tran);
            }

            print "
            </select>
            ";
            print "
            Notify
            <input type=checkbox name=notify value=1>
            <input class='btn btn-warning' type=submit value=";
            print _("Refund");
            print ">
            </form>
            ";
        }

    }

    function showChangeBalanceSubscriber () {
        $this->showPrepaidVoucherForm();
    }

    function showPrepaidVoucherForm () {

        if ($this->isEmbedded()) return true;

        $chapter=sprintf(_("Prepaid Card"));
        $this->showChapter($chapter);

        print "
        <div class='row-fluid'>
        <div class='span12'>
        ";

        printf (_("To add Credit to your account using a Prepaid Card enter it below. "));

        print "
            </div>
            </div>
        ";

        print "
        <form class=form-inline action=$this->url method=post>
        <input type=hidden name=tab value=credit>
        <input type=hidden name=issuer value=subscriber>
        <input type=hidden name=task value=Add>
        <div class=control-group>
        <div class=control>
        ";

        print "
        <input type=text size=10 name=prepaidId placeholder=\"";
        print _("Card Id");
        print "\">";
        print "<input type=text size=20 name=prepaidCard placeholder=\"";
        print _("Card Number");
        print "\">";
        if ($this->login_type != 'subscriber') {
            print " <label class=\"checkbox\">";
            print "<input type=checkbox name=notify value=1>";
            print _("Notify");
            print "</label>";
        }

        print "
        <input class='btn' type=submit value=";
        print _("Add");
        print "></div></div>
        </form>
        ";

    }


    function getPrepaidStatus() {
        dprint("getPrepaidStatus()");
        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getPrepaidStatus(array($this->sipId));

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "2000") {
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            }
        }

        $this->balance_history=$result->entries;

    }

    function getPaymentIds() {
        $transactions = array();
        $this->SipPort->addHeader($this->SoapAuth);

        $result     = $this->SipPort->getCreditHistory($this->sipId,200);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            if ($error_fault->detail->exception->errorcode != "2000") {
                printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            }
        }

        $refunded_transactions=array();
        $credit_transactions=array();

        foreach ($result->entries as $entry) {
             if (preg_match("/^CC transaction (.*)$/",$entry->description,$m)) {
                 $credit_transactions[$m[1]]=$entry->value;
             }

             if (preg_match("/^Refund (.*) with (.*)$/",$entry->description,$m)) {
                 $refunded_transactions[]=$m[1];
             }
        }

        foreach (array_keys($credit_transactions) as $tran) {
            if (!in_array($tran, $refunded_transactions)) {
                $transactions[$tran] = $credit_transactions[$tran];
            }
        }

        return $transactions;
    }

    function getTodayBalanceSummary() {

        $total_debit  = 0;
        $total_credit = 0;

        foreach ($this->balance_history as $_line) {
            $value=$_line->value;

            if (substr($_line->date,0,10) != date("Y-m-d")) {
                break;
            }

            if ($value <0) {
                $total_debit+=$value;
            }

            if ($value >0) {
                $total_credit+=$value;
            }
         }

         $total = array('debit'  => $total_debit,
                        'credit' => $total_credit
                        );
         return $total;
    }


    function showBalanceHistory() {
        $this->getBalanceHistory();

        if (!count($this->balance_history)) {
            return;
        }
        $chapter=sprintf(_("Balance History"));
        $this->showChapter($chapter);

        print "
        <div class=row-fluid>
        <div class=span12>
        ";

        $today_summary = $this->getTodayBalanceSummary();

        if ($today_summary['credit'] >= $max_credit_per_day) {
            print "<p>";
            printf (_("Today's transactions: %.2f credit, %.2f debit"), $today_summary['credit'],$today_summary['debit']);
        }

        print "
        <p>
        <table class='table table-striped table-condensed'>";
        print "<thead>";
        print "<tr>";
        print "<th class=list_header>";
        print "</th>";
        print "<th class=list_header>";
        print _("Date and Time");
        print "</th>";
        print "<th class=list_header>";
        print _("Action");
        print "</th>";
        print "<th class=list_header>";
        print _("Description");
        print "</th>";
        print "<th class=list_header align=right>";
        print _("Value");
        print "</th>";
        print "<th class=list_header align=right>";
        print _("Balance");
        print "</th>";
        print "</tr>";
        print "</thead>";

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
            <td><span class=\"label label-info\">%s</span></td>
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
            <td><span class=\"label label-important\">%s</span></td>
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
                print "<p class='form-actions'><a class=btn href=$this->url&tab=credit&action=get_balance_history&csv=1 target=_new data-original-title='";
                print _("Export");
                print "' data-content='";
                print _("Export balance history in CSV format");
                print "' rel='popover' onclick=\"window.open('$this->url&tab=credit&action=get_balance_history&csv=1]')\">";
                print _("Export");
                print "</a></p>";
            } else {
                print "<p class='form-actions'><a rel=popover class=btn href=$this->url&tab=credit&action=get_balance_history&csv=1 data-original-title='";
                print _("Export");
                print "' data-content='";
                print _("Export balance history in CSV format");
                print "' onclick=\"location.href='$this->url&tab=credit&action=get_balance_history&csv=1';\">";
                print _("Export");
                print "</a></p>";
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
            <div class='control-group $_class'>

              <label class=control-label>$pref_name</label>
              <div class='controls'>
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
                    document.sipsettings.$condition.style.display = 'inline-block';
                    document.sipsettings.$condition.style.visibility = 'visible';
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


            if ($this->CallPrefUriType[$condition]=='Other')
                $style = "visible";
            else
                $style = "hidden";

            $pref_value=$this->diversions[$condition];

            print "
                <span>
                  <input class='$style' type=text size=40
                         name=$condition value=\"$pref_value\"
                         onChange=$update_text_java(this)>
                </span>
                ";

            if ($condition=="FUNV" && $this->FUNC_access_number) {
                print "<div class=help-block>";
                printf (_("Dial %s2*X where X = Number of Minutes, 0 to Reset"), $this->access_numbers['FUNC']);
                print "</div>";
            }


            print "

              </div>
            </div>
            ";
        }

    }

    function showHeader() {
        /*print "
        <table class=settings border=0 width=100%>
        <tr>
        <td colspan=2 align=right>
        ";*/
        print "
            <div class=row-fluid>
                <div class='span12'>";
        if ($this->logoFile) {
            print "<img class='pull-right' src=./$this->logoFile border=0>";
        }

        print "
                </div>
            </div>
            ";
/*
        print "
        </td>
        </tr>
        </table>
        ";
 */
    }

    function chapterTableStart() {
    }

    function chapterTableStop() {
    }


    function getEnumMappings () {
        dprint("getEnumMappings()");

		$this->enums=array();

        $filter=array(
                      'type'     => 'sip',
                      'mapto'    => $this->account,
                      'owner'    => intval($this->owner)
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

        if ((new PEAR)->isError($result)) {
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
        printf ("<select class=input-medium name=%s>",$name);
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
        <div class='control-group'>
          <label class=control-label>";
            print _("Quick Dial");
            print "
          </label>
          <div class=controls>
            <input class=input-medium type=text size=15 maxsize=64 name=quickdial value=\"$this->quickdial\"><span class=help-inline>";
            if ($this->quickdial && preg_match("/^$this->quickdial/",$this->username)) {
                $dial_suffix=strlen($this->username) - strlen($this->quickdial);
            }
            printf (_("Prefix to auto-complete short numbers"),$dial_suffix);
            print "</span>
          </div>
        </div>
        ";
    }


    function showMobileNumber() {
        if (in_array("free-pstn",$this->groups)) {
            print "
            <div class='control-group'>
              <label for=mobile_number class=control-label>";
                print _("Mobile Number");
                printf ("
              </label>
              <div class=controls>
                <input class=input-medium type=text size=15 maxsize=64 name=mobile_number value='%s'>
              <span class=help-inline>%s</span></div></div>
            ",$this->Preferences['mobile_number'],_("International format starting with +"));
        }
    }

    function CallLimitChangePolicy() {
        if ($this->login_type == 'subscriber' and $this->call_limit_may_by_changed_by == 'reseller') {
            return false;
        }
        if ($this->login_type == 'subscriber' and $this->call_limit_may_by_changed_by == 'customer') {
            return false;
        }
        if ($this->login_type == 'subscriber' and $this->call_limit_may_by_changed_by == 'admin') {
            return false;
        }
        if ($this->login_type == 'customer' and $this->call_limit_may_by_changed_by == 'reseller') {
            return false;
        }
        if ($this->login_type == 'customer' and $this->call_limit_may_by_changed_by == 'admin') {
            return false;
        }
        if ($this->login_type == 'reseller' and $this->call_limit_may_by_changed_by == 'admin') {
            return false;
        }
        return true;
    }

    function IPAccessListChangePolicy() {
        if ($this->login_type == 'subscriber' and $this->ip_access_list_may_by_changed_by == 'reseller') {
            return false;
        }
        if ($this->login_type == 'subscriber' and $this->ip_access_list_may_by_changed_by == 'customer') {
            return false;
        }
        if ($this->login_type == 'subscriber' and $this->ip_access_list_may_by_changed_by == 'admin') {
            return false;
        }
        if ($this->login_type == 'customer' and $this->ip_access_list_may_by_changed_by == 'reseller') {
            return false;
        }
        if ($this->login_type == 'customer' and $this->ip_access_list_may_by_changed_by == 'admin') {
            return false;
        }
        if ($this->login_type == 'reseller' and $this->ip_access_list_may_by_changed_by == 'admin') {
            return false;
        }
        return true;
    }

    function showIPAccessList() {
        if (!$this->soapEngines[$this->sip_engine]['ip_access_list']) {
            return;
        }
        if (!$this->IPAccessListChangePolicy()) {
            print "
            <div class='control-group even'>
              <label class=control-label>";
                print _("IP Access List");
                printf ("
              </label>
              <div class=controls style='line-height:23px'>
                %s
              </div>
            </div>
            ",$this->ip_access_list);
        } else {
            print "
            <div class='control-group odd'>
              <label class='control-label'>";
                print _("IP Access List");
                print "
              </label>
              <div class=controls>
              <textarea class=input-medium rel=popover title data-original-title='Access List Examples' data-trigger='focus' data-toggle=\"popover\" data-content=\"";
                print _("You can limit here the IP addresses allowed to use the SIP account as an anti-fraud measure. You may enter a list of network addresses in CIDR format separated by spaces. <p>Examples:<dl><dt>0.0.0.0/0</dt><dd> means any address is allowed</dd><dt>1.2.3.4/32</dt><dd> means only one address 1.2.3.4 is allowed</dd><dt>1.2.3.0/24</dt><dd> means only the 254 IP addresses from the C class 1.2.3.0 are allowed</dd>");
                printf ("\" cols=60 rows=1 name=ip_access_list>%s</textarea>
              </div>
            </div>
            ",$this->ip_access_list);
        }
    }

    function showCallLimit() {
        if (!$this->pstn_access) {
            return;
        }

        if (!in_array("free-pstn",$this->groups)) {
            return;
        }

        if (!$this->soapEngines[$this->sip_engine]['call_limit']) {
            return;
        }

        $limit_text = sprintf(_("Default is %s"), $this->platform_call_limit);

        if (strlen($this->callLimit)) {
            $limit_text_ro=$this->callLimit;
        } else {
            $limit_text_ro=$this->platform_call_limit;
        }

        if (!$this->CallLimitChangePolicy()) {
            print "
            <div class=control-group>
              <label class=control-label>";
                print _("PSTN Call Limit");
                printf ("
              </label>
              <div class=controls>
                <span style='padding-top:5px; margin-bottom:5px;display:block;'>%s</span>
              </div>
            </div>
            ",$limit_text_ro);
        } else {
            print "
            <div class=control-group>
              <label class=control-label>";
                print _("PSTN Call Limit");
                print "
              </label>
              <div class=controls>
              <input class=input-medium rel=popover type=text size=3 name=callLimit title data-original-title='";
             print _("PSTN Call limit");
             print "' data-trigger='focus' data-toggle=\"popover\" data-content=\"";
             print "Used as anti-fraud measure for limiting the maximum number of outgoing concurrent calls to PSTN numbers.";
             printf ("\" value='%s'> %s
              </div>
            </div>
            ",$this->callLimit, $limit_text);
        }
    }

    function showCallsTab() {

        $this->getHistory();

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

        if ($this->enable_thor) {
            $cdr_source   = 'sipthor';
        } else {
            $cdr_source   = 'sip_trace';
        }

        if (count($this->calls_received)) {
            $chapter=sprintf(_("Incoming"));
            $this->showChapter($chapter);

            $j=0;
            print "<table class='table table-striped table-condensed'>";
            foreach (array_keys($this->calls_received) as $call) {
                $j++;

                $uri         = $this->calls_received[$call]['remoteParty'];
                $media="";
                foreach ($this->calls_received[$call]['media'] as $m) {
                     $media.="$m,";
                }
                $media=quoted_printable_decode($media);
                $media=rtrim($media,",");

                $duration    = normalizeTime($this->calls_received[$call]['duration']);
                $dialURI     = $this->PhoneDialURL($uri) ;
                $htmlDate    = $this->colorizeDate($this->calls_received[$call]['startTime']);
                $htmlURI     = $this->htmlURI($uri);
                $urlURI      = urlencode($this->normalizeURI($uri));

                $sessionId    = urlencode($this->calls_received[$call]['sessionId']);
                $fromTag      = urlencode($this->calls_received[$call]['fromTag']);
                $toTag        = urlencode($this->calls_received[$call]['toTag']);
                $proxyIP      = urlencode($this->calls_received[$call]['proxyIP']);
                $trace_link   = "<a href=\"javascript:void(null);\" onClick=\"return window.open('sip_trace.phtml?cdr_source=$cdr_source&callid=$sessionId&fromtag=$fromTag&totag=$toTag&proxyIP=$proxyIP', 'siptrace',
                'toolbar=0,status=0,menubar=0,scrollbars=1,resizable=1,width=1000,height=600')\">Server Logs</a>";

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
                <td width=175>$htmlDate</td>
                <td width=12><nobr>$dialURI</nobr></td>
                <td style='text-align:right' width=60>$duration</td>
                <td><nobr>$htmlURI ($media)</nobr></td>
                ";
                print "<td width=40><nobr>$trace_link</nobr></td>";
                print "<td width=19><a href=$this->url&tab=contacts&task=add&uri=$urlURI&search_text=$urlURI>$this->phonebook_img</a></td>";
                print "</td>
                </tr>
                ";
            }
            print "</table>";
        }

        if (count($this->calls_placed)) {
            $chapter=sprintf(_("Outgoing"));
            $this->showChapter($chapter);

            $j=0;

            print "<table class='table table-striped table-condensed'>";
            foreach (array_keys($this->calls_placed) as $call) {
                $j++;

                if ($this->calls_placed[$call]['to'] == "sip:".$this->voicemail['Account'] ) {
                    continue;
                }

                $uri = $this->calls_placed[$call]['remoteParty'];
                $media = "";
                foreach ($this->calls_placed[$call]['media'] as $m) {
                     $media.="$m,";
                }
                $media=rtrim($media,",");
                $price       = $this->calls_placed[$call]['price'];
                $status      = $this->calls_placed[$call]['status'];
                $rateinfo    = $this->calls_placed[$call]['rateInfo'];
                $duration    = normalizeTime($this->calls_placed[$call]['duration']);
                $dialURI     = $this->PhoneDialURL($uri) ;
                $htmlDate    = $this->colorizeDate($this->calls_placed[$call]['startTime']);
                $stopTime    = $this->calls_placed[$call]['stopTime'];
                $htmlURI     = $this->htmlURI($uri);
                $urlURI      = urlencode($this->normalizeURI($uri));

                $sessionId    = urlencode($this->calls_placed[$call]['sessionId']);
                $fromTag      = urlencode($this->calls_placed[$call]['fromTag']);
                $toTag        = urlencode($this->calls_placed[$call]['toTag']);
                $proxyIP      = urlencode($this->calls_placed[$call]['proxyIP']);
                $trace_link   = "<a href=\"javascript:void(null);\" onClick=\"return window.open('sip_trace.phtml?cdr_source=$cdr_source&callid=$sessionId&fromtag=$fromTag&totag=$toTag&proxyIP=$proxyIP', 'siptrace',
                'toolbar=0,status=0,menubar=0,scrollbars=1,resizable=1,width=1000,height=600')\">Server Logs</a>";

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
                if (!$stopTime) {
                    $duration = _('In progress');
                }

                print "
                <tr class=$_class>
                <td width=175>$htmlDate</td>
                <td width=12><nobr>$dialURI<nobr></td>
                <td style='text-align:right' width=75>$duration</td>
                <td ><nobr>$htmlURI ($media) $price_print</nobr></td>
                ";

                print "<td width=40><nobr>$trace_link</nobr></td>";
                print "<td width=19><a href=$this->url&tab=contacts&task=add&uri=$urlURI&search_text=$urlURI>$this->phonebook_img</a></td>";
                print "</td>
                </tr>
                ";
            }
            print "</table>";
        }

    }

    function getHistory($status = 'all')
    {
        dprint("getHistory()");

        $fromDate = time() - 3600 * 24 * 14; // last two weeks
        $toDate = time();

        $CallQuery = array(
            "fromDate" => $fromDate,
            "toDate"   => $toDate,
            "limit"    => 50
        );

        $CallsQuery = array(
            "placed"   => $CallQuery,
            "received" => $CallQuery
        );

        $this->SipPort->addHeader($this->SoapAuth);
        $result = $this->SipPort->getCalls($this->sipId, $CallsQuery);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf(
                "<p><font color=red>Error (SipPort): %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return false;
        }

        // received calls
        foreach ($result->received as $callStructure) {
            $media = array();
            $apps = explode(",", quoted_printable_decode($callStructure->applicationTypes[0]));
            foreach ($apps as $app) {
                $media[] = trim($app);
            }
            if (!$callStructure->stopTime && $status == 'completed') {
                continue;
            }

            $fromHeader = quoted_printable_decode($callStructure->fromHeader);

            $this->calls_received[] = array(
                "remoteParty"  => quoted_printable_decode($callStructure->fromURI),
                "displayName"  => getDisplayNameFromFromHeader($fromHeader),
                "startTime"    => getLocalTime($this->timezone, $callStructure->startTime),
                "stopTime"     => getLocalTime($this->timezone, $callStructure->stopTime),
                "timezone"     => $this->timezone,
                "duration"     => $callStructure->duration,
                "status"       => $callStructure->status,
                "sessionId"    => quoted_printable_decode($callStructure->sessionId),
                "fromTag"      => quoted_printable_decode($callStructure->fromTag),
                "toTag"        => quoted_printable_decode($callStructure->toTag),
                "proxyIP"      => $callStructure->proxyIP,
                "media"        => $media
            );
        }

        // placed calls
        foreach ($result->placed as $callStructure) {
            if ($callStructure->status == 435) continue;

            $media = array();
            $apps = explode(",", quoted_printable_decode($callStructure->applicationTypes[0]));
            foreach ($apps as $app) {
                $media[] = trim($app);
            }
            if (!$callStructure->stopTime && $status == 'completed') {
                continue;
            }

            $fromHeader = quoted_printable_decode($callStructure->fromHeader);

            $this->calls_placed[] = array(
                "remoteParty"  => quoted_printable_decode($callStructure->toURI),
                "displayName"  => getDisplayNameFromFromHeader($fromHeader),
                "startTime"    => getLocalTime($this->timezone, $callStructure->startTime),
                "stopTime"     => getLocalTime($this->timezone, $callStructure->stopTime),
                "timezone"     => $this->timezone,
                "duration"     => $callStructure->duration,
                "status"       => $callStructure->status,
                "price"        => $callStructure->price,
                "sessionId"    => quoted_printable_decode($callStructure->sessionId),
                "fromTag"      => quoted_printable_decode($callStructure->fromTag),
                "toTag"        => quoted_printable_decode($callStructure->toTag),
                "proxyIP"      => $callStructure->proxyIP,
                "media"        => $media
            );
        }

        $this->call_history = array(
            'placed'   => $this->calls_placed,
            'received' => $this->calls_received
        );
    }

    function getCallStatistics()
    {
        dprint("getCallStatistics()");

        $fromDate = mktime(0, 0, 0, date("m"), "01", date("Y"));
        $toDate = time();

        $CallQuery = array(
            "fromDate" => $fromDate,
            "toDate"   => $toDate
        );

        $CallQuery = array(
            "limit" => 1
        );

        $CallsQuery = array(
            "placed" => $CallQuery
        );

        $this->SipPort->addHeader($this->SoapAuth);
        $result = $this->SipPort->getCallStatistics($this->sipId, $CallsQuery);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf(
                "<p><font color=red>Error (SipPort): %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return false;
        }

        //dprint_r($result);

        $this->thisMonth['calls'] = $result->placed->calls;
        $this->thisMonth['price'] = $result->placed->price;
    }

    function addPhonebookEntry()
    {
        dprint("addPhonebookEntry()");

        $uri       = strtolower(trim($_REQUEST['uri']));
        $name      = trim($_REQUEST['name']);
        $group     = trim($_REQUEST['group']);

        if (!strlen($uri)) return false;

        $phonebookEntry = array(
            'uri'       => $uri,
            'name'      => $name,
            'group'     => $group
        );

        dprint("addPhonebookEntry");
        $this->SipPort->addHeader($this->SoapAuth);
        $result = $this->SipPort->addPhoneBookEntry($this->sipId, $phonebookEntry);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf(
                "
                <div class=row-fluid>
                    <div class=span12>
                        <span class='alert alert-error'>Error (SipPort): %s (%s): %s</span>
                    </div>
                </div>
                ",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return false;
        }
        return true;
    }

    function updatePhonebookEntry()
    {
        dprint("updatePhonebookEntry()");

        $uri       = strtolower(trim($_REQUEST['uri']));
        $group     = trim($_REQUEST['group']);

        $name = trim($_REQUEST['name']);
        $phonebookEntry = array(
            'name'  => $name,
            'uri'   => $uri,
            'group' => $group
        );
        //dprint_r($phonebookEntry);

        dprint("updatePhonebookEntry");
        $this->SipPort->addHeader($this->SoapAuth);
        $result = $this->SipPort->updatePhoneBookEntry($this->sipId, $phonebookEntry);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf(
                "<p><font color=red>Error (SipPort): %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return false;
        }
        return true;
    }

    function deletePhonebookEntry()
    {
        dprint("deletePhonebookEntry()");

        $uri = strtolower($_REQUEST['uri']);

        dprint("deletePhonebookEntry");
        $this->SipPort->addHeader($this->SoapAuth);
        $result = $this->SipPort->deletePhoneBookEntry($this->sipId, $uri);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf(
                "<p><font color=red>Error (SipPort): %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return false;
        }
        return true;
    }

    function getPhoneBookEntries()
    {
        dprint("getPhoneBookEntries()");

        if ($_REQUEST['task'] == 'search') {
            $search_text = trim($_REQUEST['uri']);
        }
        $group = trim($_REQUEST['group']);

        if (!strlen($search_text)) $search_text="%" ;

        $match = array(
            'uri'  => '%'.$search_text.'%',
            'name' => '%'.$search_text.'%'
        );

        if (strlen($group)) {
            if ($group == "empty") {
                $match['group'] = '';
            } else {
                $match['group'] = $group;
            }
        }

        $range = array(
            'start' => 0,
            'count' => 100
        );

        $this->SipPort->addHeader($this->SoapAuth);
        $result = $this->SipPort->getPhoneBookEntries($this->sipId, $match, $range);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf(
                "<p><font color=red>Error (SipPort): %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            return false;
        }

        $this->PhonebookEntries=$result->entries;
        //dprint_r($this->PhonebookEntries);
    }

    function showContactsTab()
    {
        dprint("showContactsTab()");

        if ($this->show_directory) {
            $chapter = sprintf(_("Directory"));
            $this->showChapter($chapter);

            print "<div class=row-fluid><div class=span12>";
            print _("To find other SIP Addresses fill in the First Name or the Last Name and click the Search button. ");
            print "</div>";

            $this->showSearchDirectory();

            print "</div>";
        }

        if ($this->rows || $_REQUEST['task'] == 'directory') {
            // hide local contacts if we found a global contact
            return true;
        }

        $chapter = sprintf(_("Don't Disturb")).' '.sprintf(_("Groups"));
        $this->showChapter($chapter);

        print "<div class=row-fluid><div class=span12>";
        print _("You can organize contacts into groups that can be used to accept incoming calls in Don't Disturb section. ");
        print "</div></div>";

        $adminonly = $_REQUEST['adminonly'];
        $accept    = $_REQUEST['accept']; // selected search group;

        $task      = $_REQUEST['task'];
        //if ($task == "search" ){
            $search_text = $_REQUEST['uri'];
       // }

        $confirm  = $_REQUEST['confirm'];

        $group    = $_REQUEST['group'];
        $uri      = $_REQUEST['uri'];

        $name     = $_REQUEST['name'];

        if ($task == "deleteContact" && $confirm) {
            $this->deletePhonebookEntry();
            unset($task);
            unset($confirm);
        } elseif ($task == "update") {
            $this->updatePhonebookEntry();
            unset($task);
        } elseif ($task == "add") {
            $this->addPhonebookEntry();
            unset($task);
        }

        $this->getPhoneBookEntries();
        $maxrowsperpage = 250;

        $url_string = $this->url."&tab=contacts";

        printf(
            "
            <script type=\"text/javascript\">
            <!--//

                function toggleVisibility(rowid) {
                    if (document.getElementById) {
                        row = document.getElementById(rowid);
                        if (row.style.display == 'block') {
                            row.style.display = 'none';
                        } else {
                            row.style.display = 'block';
                        }
                        return false;
                    } else {
                        return true;
                    }
                }

                function changeAction(url) {
                    if (url ==='add') {
                        document.forms.contacts.action='%s&task=add';
                    } else {
                        document.forms.contacts.action='%s&task=search';
                    }
                }

                //-->
            </script>

            <form class=form-inline name=contacts action=%s method=post>
            <input type=hidden name=tab value=contacts>
            <div class='control-group'>
                <div class='control'>
                    <input class=span3 type=text size=20 name=uri placeholder=\"%s\">
                    <div class=input-append>
            ",
            $this->url,
            $this->url,
            $this->url,
            _("Add sip address or search for contacts")
        );

        if (count($this->PhonebookEntries) || $task == "search"){
            $selected[$group] = "selected";

            printf(
                "
                <select class=span2 name=group>
                    <option value=\"\">
                    %s
                    </option>
                ",
                _('Group')
            );

            foreach (array_keys($this->PhonebookGroups) as $key) {
                printf(
                    "<option value=\"%s\" %s>%si</option>",
                    $key,
                    $selected[$key],
                    $this->PhonebookGroups[$key]
                );
            }
            printf(
                "
                <option value=\"\">------</option>
                <option value=\"empty\" %s>%s</option>
                </select>
                <input class='btn btn-primary' type=submit onClick='changeAction(\"search\")' value=%s>
                ",
                $selected['empty'],
                _("No group"),
                _("Search")
            );
        }

        printf(
            "
            <input class='btn' type=submit onclick='changeAction(\"add\")' value=%s>
            </div>
            <span class=help-inline>%s</span>
            </div>
            </div>
            </form>
            ",
            _("Add"),
            _("(wildcard %)")
        );

        if (count($this->PhonebookEntries)){
            print "
            <p>
            <table class='table table-striped table-condensed' width=100% cellpadding=1 cellspacing=1 border=0>
            <thead>
            <tr>
            <th class=list_header align=right></td>
            ";
            print "<th class=list_header>";
            print _("SIP Address");
            print "</td>";
            print "<th class=list_header>";
            print "</th>";
            print "<th class=list_header>";
            print _("Display Name");
            print "</th>";
            print "<th class=list_header>";
            print _("Group");
            print "</th>";
            print "<th class=list_header>";
            print _("Action");
            print "</th>";
            print "</tr></thead>";

            foreach (array_keys($this->PhonebookEntries) as $_entry) {
                $found = $i + 1;

                $rr = floor($found / 2);
                $mod = $found - $rr * 2;

                if ($mod == 0) {
                    $_class = 'odd';
                } else {
                    $_class = 'even';
                }

                printf(
                    "
                    <tr class='%s contacts_table'>
                    <form name=\"Entry$%s\" class=form-inline action=\"%s&tab=%s\">
                    %s
                    <input type=hidden name=tab value=\"%s\">
                    <input type=hidden name=task value=\"update\">
                    <input type=hidden name=uri value=\"%s\">
                    <td>%s</td><td>%s</td>
                    ",
                    $_class,
                    $found,
                    $this->url,
                    $this->tab,
                    $this->hiddenElements,
                    $this->tab,
                    $this->PhonebookEntries[$_entry]->uri,
                    $found,
                    $this->PhonebookEntries[$_entry]->uri
                );


                if (preg_match("/\%/", $this->PhonebookEntries[$_entry]->uri)) {
                    print "<td></td><td>";
                } else {
                    printf(
                        "
                        <td>%s</td>
                        <td>
                        ",
                        $this->PhoneDialURL($this->PhonebookEntries[$_entry]->uri)
                    );
                }

                printf("<div class='input-append' style='margin-bottom:0px' ><input style='margin-bottom:0' type=text name=name value='%s'>", $this->PhonebookEntries[$_entry]->name);
                printf("<a class=btn href=\"javascript: document.Entry$found.submit()\">%s</a></div>", _("Update"));

                print "</td><td>";
                printf(
                    "<select style='margin-bottom:0' name=group onChange=\"location.href='%s&task=update&uri=%s&name=%s&group='+this.options[this.selectedIndex].value\">",
                    $url_string,
                    urlencode($this->PhonebookEntries[$_entry]->uri),
                    urlencode($this->PhonebookEntries[$_entry]->name)
                );

                print "<option value=\"\">";
                $selected_grp[$this->PhonebookEntries[$_entry]->group] = "selected";
                foreach (array_keys($this->PhonebookGroups) as $_key) {
                    printf(
                        "<option value=\"%s\" %s>%s</option>",
                        $_key,
                        $selected_grp[$_key],
                        $this->PhonebookGroups[$_key]
                    );
                }
                unset($selected_grp);

                print "</select>";
                print "</td>";

                if ($task == "deleteContact" && $uri == $this->PhonebookEntries[$_entry]->uri) {
                    print "<td bgcolor=red style='vertical-align: middle'>";
                    printf(
                        "<a href=%s&task=deleteContact&uri=%s&confirm=1&search_text=%s>",
                        $url_string,
                        urlencode($this->PhonebookEntries[$_entry]->uri),
                        urlencode($search_text)
                    );
                    print _("Confirm");
                } else {
                    print "<td>";
                    printf(
                        "<a href=%s&task=deleteContact&uri=%s&search_text=%s>",
                        $url_string,
                        urlencode($this->PhonebookEntries[$_entry]->uri),
                        urlencode($search_text)
                    );
                    if ($this->delete_img) {
                        //print $this->delete_img;
                        print "<i class=\"icon-remove\"></i>";
                    } else {
                        print _("Delete");
                    }
                }
                print "</a>";
                print "</td></form></tr>";
                $i++;
            }
            print "</table>";
            print "</td></tr></table>";
        }
    }

    function exportPhonebook($userAgent)
    {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error (SipPort): %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }
    }

    function getJournalEntries() {
        $this->journalEntries['success']       = false;
        $this->journalEntries['error_message'] = NULL;
        $this->journalEntries['results']       = array();
        $return_summary = $_REQUEST['summary'];

        if ($this->chat_replication_backend == 'mysql') {
            $this->db = new DB_CDRTool();

            $where="";
            if ($_REQUEST['except_uuid']) {
                $where.= sprintf(" and uuid <> '%s'", addslashes($_REQUEST['except_uuid']));
            }
            if ($_REQUEST['after_id']) {
                $after_id = intval($_REQUEST['after_id']);
            } else {
                $after_id = 0;
            }

            $where.= sprintf(" and id > %d", addslashes($after_id));

            if ($_REQUEST['after_timestamp']) {
                $where.= sprintf(" and timestamp > '%s'", addslashes($_REQUEST['after_timestamp']));
            }
            if ($_REQUEST['limit'] and intval($_REQUEST['limit']) < 5000) {
                $limit = intval($limit);
            } else {
                $limit = 5000;
            }

            $query=sprintf("select * from client_journal where account = '%s' %s order by timestamp ASC limit %d",  addslashes($this->account), $where, $limit);
            if (!$this->db->query($query)) {
                $this->journalEntries['error_message'] = 'Database Failure';
                $this->journalEntries['rows'] = 0;
                return false;
            } else {
                $this->journalEntries['success'] = true;
                $this->journalEntries['rows'] = $this->db->num_rows();
            }

            if ($this->db->num_rows()) {
                while ($this->db->next_record()) {
                    $entry = array(
                                   'id'          => $this->db->f('id'),
                                   'source'      => 'default',
                                   'timestamp'   => $this->db->f('timestamp'),
                                   'account'     => $this->db->f('account'),
                                   'uuid'        => $this->db->f('uuid'),
                                   'ip_address'  => $this->db->f('ip_address'),
                                   'data'        => $this->db->f('data')
                                   );
                    $this->journalEntries['results'][]=$entry;
                }
            }
        } else {
            if (!$this->getMongoJournalTable()) {
                $result['success'] = false;
                $result['error_message'] = $this->mongo_exception;
                return $result;
            }

            $mongo_where=array();
            $mongo_where['account'] = $this->account;
            if ($_REQUEST['except_uuid']) {
                $mongo_where['uuid'] = array('$ne' => $_REQUEST['except_uuid']);
            }

            if ($_REQUEST['after_timestamp']) {
                $mongo_where['timestamp'] = array('$gt' => intval($_REQUEST['after_timestamp']));
            }

            if ($_REQUEST['limit'] and intval($_REQUEST['limit']) < 5000) {
                $limit = intval($limit);
            } else {
                $limit = 5000;
            }

            $cursor = $this->mongo_table_ro->find($mongo_where)->sort(array('timestamp'=>1))->limit($limit)->slaveOkay();
            $this->journalEntries['success'] = true;
            $this->journalEntries['rows'] = $cursor->count();

            foreach ($cursor as $result) {
                $entry = array(
                               'id'          => strval($result['_id']),
                               'source'      => 'default',
                               'timestamp'   => $result['timestamp'],
                               'account'     => $result['account'],
                               'uuid'        => $result['uuid'],
                               'ip_address'  => $result['ip_address'],
                               'data'        => $result['data']
                               );
                $this->journalEntries['results'][]=$entry;
            }

            if ($return_summary) {
                $mongo_where=array();
                $mongo_where['account'] = $this->account;
                if ($_REQUEST['except_uuid']) {
                    $mongo_where['uuid'] = array('$ne' => $_REQUEST['except_uuid']);
                }

                if ($_REQUEST['limit'] and intval($_REQUEST['limit']) < 5000) {
                    $limit = intval($limit);
                } else {
                    $limit = 5000;
                }

                $cursor = $this->mongo_table_ro->find($mongo_where)->sort(array('timestamp'=>1))->limit($limit)->slaveOkay();
                foreach ($cursor as $result) {
                    $entry = array(
                                   'journal_id'  => strval($result['_id']),
                                   'timestamp'   => $result['timestamp'],
                                   );
                    $this->journalEntries['summary'][]=$entry;
                }
            }

        }
        return True;
    }

    function putJournalEntries() {
        $result['results'] = array();
        if (strlen($_REQUEST['uuid'])) {
            $uuid = $_REQUEST['uuid'];
        } else {
            $result['success'] = false;
            $result['error_message'] = 'Missing uuid';
            return $result;
        }
        if (strlen($_REQUEST['data'])) {
            $data = $_REQUEST['data'];
        } else {
            $result['success'] = false;
            $result['error_message'] = 'Missing data';
            return $result;
        }

        if ($this->chat_replication_backend == 'mysql') {
            $this->db = new DB_CDRTool();
        } else if ($this->chat_replication_backend == 'mongo') {
            if (!$this->getMongoJournalTable()) {
                $result['success'] = false;
                $result['error_message'] = $this->mongo_exception;
                return $result;
            }
        }
        if ($rows=json_decode($data)) {
            foreach ($rows as $row) {
                if (!property_exists($row, 'data')) {
                    continue;
                }

                $entry = $row->data;
                if (property_exists($row, 'action')) {
                    $action = $row->action;
                } else {
                    $action = 'add';
                }
                if ($this->chat_replication_backend == 'mysql') {
                    $query=sprintf("insert into client_journal (timestamp, account, uuid, data, ip_address) values (NOW(),'%s', '%s', '%s', '%s')", addslashes($this->account), addslashes($uuid), addslashes($entry), addslashes($_SERVER['REMOTE_ADDR']));
                    if (!$this->db->query($query)) {
                        $result['results'][]=array('id'         => $row->id,
                                                   'journal_id' => NULL,
                                                   'source'     => 'default'
                                                );
                    } else {
                        $query="select LAST_INSERT_ID() as id";
                        $this->db->query($query);
                        $this->db->next_record();
                        $id = $this->db->f('id');
                        $result['results'][]=array('id'         => $row->id,
                                                   'journal_id' => $id,
                                                   'source'     => 'default'
                                                   );
                    }

                } else if ($this->chat_replication_backend == 'mongo') {
                    if ($action == 'add') {
                        $timestamp = time();
                        $mongo_query=array('timestamp'  => $timestamp,
                                           'datetime'   => Date("Y-m-d H:i:s", $timestamp),
                                           'account'    => $this->account,
                                           'uuid'       => $uuid,
                                           'data'       => $entry,
                                           'ip_address' => $_SERVER['REMOTE_ADDR']
                                           );

                        $this->mongo_table_rw->insert($mongo_query);
                        if ($mongo_query['_id']) {
                            $mongo_id = strval($mongo_query['_id']);
                            $result['results'][]=array('id'         => $row->id,
                                                       'journal_id' => $mongo_id,
                                                       'source'     => 'default'
                                                       );
                        } else {
                            $result['results'][]=array('id'         => $row->id,
                                                       'journal_id' => NULL,
                                                       'source'     => 'default'
                                                    );
                        }
                    } else if ($action == 'remove') {
                        if (property_exists($row, 'journal_id')) {
                            $mongo_query=array(
                                               'account'    => $this->account,
                                               'journal_id' => $row->journal_id
                                               );

                            $this->mongo_table_rw->remove($mongo_query);
                            $result['results'][]=array('id'         => NULL,
                                                       'journal_id' => $row->journal_id
                                                      );
                        }
                    }
                }
            }
            $result['success'] = true;
        } else {
            $result['success'] = false;
            $result['error_message'] = 'Json decode error';
        }
        return $result;
    }

    function deleteJournalEntries() {
        if (strlen($_REQUEST['data'])) {
            $data = $_REQUEST['data'];
            $entries = json_decode($data);
        } else {
            if (strlen($_REQUEST['journal_id'])) {
               $entries=array($_REQUEST['journal_id']);
            } else {
                $result['success'] = false;
                $result['error_message'] = 'Missing data';
                return $result;
            }
        }

        if ($this->chat_replication_backend == 'mysql') {
            $this->db = new DB_CDRTool();
        } else if ($this->chat_replication_backend == 'mongo') {
            if (!$this->getMongoJournalTable()) {
                $result['success'] = false;
                $result['error_message'] = $this->mongo_exception;
                return $result;
            }
        }

        if ($entries) {
            if ($this->chat_replication_backend == 'mysql') {
                $journal_id_sql="";
                foreach ($entries as $entry) {
                    $journal_id_sql.=sprintf("'%s',",$entry);
                }
                $journal_id_sql = rtrim($journal_id_sql,",");
                $query=sprintf("delete from client_journal where account in '%s' and journal_id in (%s)", addslashes($this->account), addslashes($journal_id_sql));
                if (!$this->db->query($query)) {
                    $result['error_message'] = 'database error';
                } else {
                    $result['success'] = true;
                }

            } else if ($this->chat_replication_backend == 'mongo') {
                $id_entries=array();
                foreach ($entries as $entry) {
                    $id_entries[] = new MongoId($entry);
                }
                $mongo_query=array('account' => $this->account,
                                   '_id'     => array('$in'=> $id_entries)
                                   );

                $this->mongo_table_rw->remove($mongo_query);
                $result['success'] = true;
            }
        } else {
            $result['success'] = false;
            $result['error_message'] = 'No journal entries provided';
        }
        return $result;
    }

    function getAcceptRules() {

        dprint("getAcceptRules()");

        $this->SipPort->addHeader($this->SoapAuth);
        $result     = $this->SipPort->getAcceptRules($this->sipId);

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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
        <form method=post class=form-horizontal name=sipsettings onSubmit=\"return checkForm(this)\">
        ";

        print "
        <div class=row-fluid>
            <div class=span12>
                <p>";
        print _("You can reject calls depending on the time of day and Caller-ID. ");
        print _("You can create custom groups in the Contacts page like Family or Coworkers. ");
        print  "</p>
            <p>";
        print _("Rejected calls are diverted based on the Unavailable condition in the Call Forwarding page. ");
        print "<p>";
        print "<p class=desc>";
        printf (_("Your current time is: %s"),$this->timezone);
        $timestamp=time();
        $LocalTime=getLocalTime($this->timezone,$timestamp);
        print " $LocalTime";

        print "
        </div>
        </div>
        ";

       // $chapter=sprintf(_("Rules"));
       // $this->showChapter($chapter);

/*  print "
        <div class=row-fluid>
        <div class=span12>";
        print _("This will override the permanent rules for the chosen duration. ");
        print "
        </div>
        </div>
        ";
 */
        if ($this->acceptRules['temporary']['duration']) {
            $class_e='error';
        } else {
            $class_e='';
        }

       // print "<div class='control-group $class_e'>
         //   <label for=duration class=control-label>";

      //  print _("Duration");
       // print "</label>
            //";

         // print "<span class=help-inline>";
       // print _("minute(s)");
       // print "</span>";


        //print "</span></div>
        //</div>

        //";

        $chapter=sprintf(_("Rules"));
        $this->showChapter($chapter);

        print "
        <div class=row-fluid>
        ";

        print "<table class='table table-condensed table-striped middle' border=0 width=100%>";
        print "<thead><tr>
            <tr>
            <th colspan=6>
            ";
        print _("Temporary");
        print "</th></tr></thead>";
        print "<tr><td style='vertical-align: middle'><span>";
        print _("Duration");
        print "</span></td><td colspan='2' style='vertical-align: middle'>";
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

                    print " <input type=text name=minutes size=3 maxsize=3 value=\"";
                    print $this->acceptRules['temporary']['duration'];
                    print "\" disabled=true>";
                    print " <input type=hidden name=accept_temporary_remain value=\"";
                    print $this->acceptRules['temporary']['duration'];
                    print "\"> ";
            } else {
                print "<select id=testselect rel='popover' class=input-medium name=duration data-original-title='";
                print _("Temporary Rules");
                print "' data-content='";
                    print _("This will override the permanent rules for the chosen duration.");
                    print "'> ";
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
                    print "</select><span> ";
                    print _("Minute(s)");
                    print "</span>";
            }
        print "</td>";

        $_name="radio_temporary";

        $_checked_everybody="";
        $_checked_nobody="";
        $_checked_groups="";

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

        printf ("<td style='vertical-align:middle' class='note'><input type=radio name=%s value=0 %s> %s</td> ",$_name,$_checked_everybody,_("Everybody"));
        printf ("<td style='vertical-align:middle' class='$class_nobody'><input type=radio name=%s value=1 %s> %s </td>",$_name,$_checked_nobody,_("Nobody"));

        $c=count($this->acceptRules['groups']);

        if ($_checked_groups) {
            $class_groups="checked_groups";
        } else {
            $class_groups="note";
        }

        print "<td style='vertical-align:middle' class='$class_groups'>";

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
                printf ("<span><input style='vertical-align:top' type=checkbox name=%s value=%s onClick=\"document.sipsettings.radio_temporary[2].checked=true\" %s> %s</span>\n",
                $_name,
                $this->acceptRules['groups'][$_group],
                $_checked,
                $this->PhonebookGroups[$this->acceptRules['groups'][$_group]]
                );
            }
        }

        print "</td></tr>";
        print "<thead>
            <th colspan=6>
            ";
        print _("Permanent");
        print "</th></tr><tr>
            <th>";
        print _("Days");
        print "</th>
        <th colspan=2>";
        print _("Time Interval");
        print "</th>
        <th colspan=3>";
        print _("Groups");
        print "</th>
        </tr></thead>
        ";

        foreach (array_keys($this->acceptDailyProfiles) as $profile) {

            if ($this->acceptRules['persistent'][$profile]['start'] || $this->acceptRules['persistent'][$profile]['stop']) {
                $class="checked_groups";
                $class2="label label-info";
            } else {
                $class="mhj";
                $class2='';
            }
            if ($profile==1) {
                print "<tr><td colspan=6 style='height:3px; padding:0px' bgcolor=lightgrey></td></tr>";
            }

            print "
            <tr>
            <td style='vertical-align: middle;'><span class='$class2'>";

            printf ("%s",$this->acceptDailyProfiles[$profile]);
            print "</span></td>";
            unset($selected_StartTime);
            $selected_StartTime[$this->acceptRules['persistent'][$profile]['start']]="selected";
            printf ("<td><select class=span10 name=start_%s>",$profile);

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
            printf ("<td><select class=span10 name=stop_%s>",$profile);
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

            printf ("<td style='vertical-align: middle;' class='note'><input style='vertical-align: top;' type=radio name=%s value=0 %s> %s</td>",$_name,$_checked_everybody,_("Everybody"));
            printf ("<td style='vertical-align: middle;' class='$class_nobody'><input style='vertical-align: top;' type=radio name=%s value=1 %s> %s</td>",$_name,$_checked_nobody,_("Nobody"));

            $c=count($this->acceptRules['groups']);

            if ($_checked_groups) {
                $class_groups="checked_groups";
            } else {
                $class_groups="note";
            }

            print "<td style='vertical-align: middle' class='controls $class_groups'>";
            if (count($this->acceptRules['groups'])>2) {
                printf ("<input style='vertical-align: top;' type=radio name=%s value=2 %s class=hidden>",$_name,$_checked_groups);
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
                    printf ("<input style='vertical-align: top;' type=checkbox name=%s value=%s onClick=\"document.sipsettings.radio_persistent_%s[2].checked=true\" %s> %s ",
                    $_name,
                    $this->acceptRules['groups'][$_group],
                    $profile,
                    $_checked,
                    $this->PhonebookGroups[$this->acceptRules['groups'][$_group]]
                    );
                }
            }

            print "</td>
            </tr>
            ";
        }

        print "</table></div>";

        print "
        <div class='form-actions'>
            <input type=hidden name=action value=\"set accept rules\">
        ";

        print "
        <input class='btn' type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
        ";

        print "
          </div>
        ";

        print $this->hiddenElements;
        print "
        </form>
        ";

        $chapter=sprintf(_("Rejected Callers"));
        $this->showChapter($chapter);

        print "
        <form class=form-horizontal method=post name=reject_form onSubmit=\"return checkForm(this)\">
        ";
        print "
        <div class=row-fluid>
        <div class=span12>";
        print _("Use %Number@% to match PSTN numbers and user@domain to match SIP Addresses");
        print "
        </div>
        </div>
        ";

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
                <div class='control-group $_class'>";

                print "<label class=control-label>";
                print _("SIP Address");
                print "</label>";
                print "<div class=controls>
                <input type=text size=35 name=rejectMembers[] value=\"$_member\">
                </div>";
                print "</div>";
            }
        }

        print "<div class=control-group>";
        print "<label class=control-label>";
        print _("SIP Address");
        print "</label>";
        print "<div class=controls>
            <input type=text size=35 name=rejectMembers[]>";
        print "</div></div>";
        print "<div class='form-actions'>";
        print "<input class=btn type=submit value=\"";
        print _("Save");
        print "\"
               onClick=saveHandler(this)>
            ";
        print "</div></div>";

        print "
            <input type=hidden name=action value=\"set reject\">
        ";


        print $this->hiddenElements;

        print "
        </form>
        ";


    }

    function deleteAccount($skip_html=False) {
        dprint ("SipSettings->deleteAccount($this->account, $this->email)");

        $this->getBalanceHistory();

        if (count($this->balance_history) != "0" && $this->login_type == 'subscriber') {
            return false;
        }

        if (!$this->email && !$skip_html) {
            print "<p><font color=blue>";
            print _("Please fill in the e-mail address. ");
            print "</font>";
            return false;
        }

        $subject = sprintf("Removal of SIP account %s",$this->account);
        //$this->expire_date = new DateTime('now');

        $this->expire_date = date("Y-m-d H:i:s",strtotime("+2 days"));
        $this->ip = $_SERVER['REMOTE_ADDR'];

        $tpl_html = $this->getEmailDeleteTemplateHTML($this->reseller, $this->Preferences['language']);
        //dprint("$tpl_html");
        if (!$tpl_html && !$skip_html) {
            print "<p><font color=red>";
            print _("Error: no HTML email template found");
            print "</font>";
            return false;
        }

        //print "$tpl_html";
        define("SMARTY_DIR", "/usr/share/php/smarty/libs/");
        include_once(SMARTY_DIR . 'Smarty.class.php');

        $smarty = new Smarty;
        $smarty->template_dir = '.';

        //$smarty->use_sub_dirs = true;
        //$smarty->cache_dir = 'templates_c';
        $smarty->assign('client', $this);
        //print"$this->sip_settings_page";
        if ($tpl_html) {
            $bodyhtml = $smarty->fetch($tpl_html);
        }
        include_once 'Mail.php';
        include_once 'Mail/mime.php' ;

        $hdrs = array(
            'From'    => $this->support_email,
            'Subject' => $subject,
//            'Cc'      => $this->support_email
        );
        //dprint("1");
        $crlf = "\n";
        $mime = new Mail_mime($crlf);

        if ($tpl_html) {
          $mime->setHTMLBody($bodyhtml);
        }

        $body = $mime->get();
        $hdrs = $mime->headers($hdrs);

        $mail =& Mail::factory('mail');

        if ($mail->send($this->email, $hdrs, $body)) {
            if (!$skip_html) {
                $this->Preferences['account_delete_request']=1;
                $this->saveSettings();
                $this->getAccount($this->account);
                print "<p>";
                printf (_("Removal email has been sent to %s"), $this->email);
            }
            return 1;
        }
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

        //if ($_REQUEST['sip_filter'] == '1') {
        //    $identifier = $this->RandomIdentifier();
        //}

        $tpl = $this->getEmailTemplate($this->reseller, $this->Preferences['language']);

        if (!$tpl && !$skip_html) {
            print "<p><font color=red>";
            print _("Error: no email template found");
            print "</font>";
            return false;
        }

        $tpl_html = $this->getEmailTemplateHTML($this->reseller, $this->Preferences['language']);

        //if (!$tpl_html && !$skip_html) {
        //    print "<p><font color=red>";
        //     print _("Error: no HTML email template found");
        //    print "</font>";
        //}

        if (!$this->store_clear_text_password) {
                    $web_password = '';
        }
        if (in_array("free-pstn",$this->groups)) $this->allowPSTN=1; // used by smarty

        define("SMARTY_DIR", "/usr/share/php/smarty/libs/");
        include_once(SMARTY_DIR . 'Smarty.class.php');

        $smarty = new Smarty;
        $smarty->template_dir = '.';

        //$smarty->use_sub_dirs = true;
        //$smarty->cache_dir = 'templates_c';

        $smarty->assign('client', $this);
        $bodyt = $smarty->fetch($tpl);

        if ($tpl_html) {
            $bodyhtml = $smarty->fetch($tpl_html);
        }

        include_once 'Mail.php';
        include_once 'Mail/mime.php' ;

        $hdrs = array(
            'From'    => $this->support_email,
            'Subject' => $subject
        );

        $crlf = "\n";
        $mime = new Mail_mime($crlf);

        $mime->setTXTBody($bodyt);

        if ($tpl_html) {
          $mime->setHTMLBody($bodyhtml);
        }

        $body = $mime->get();
        $hdrs = $mime->headers($hdrs);

        $mail =& Mail::factory('mail');

        //dprint_r($_REQUEST);

        if ($mail->send($this->email, $hdrs, $body)) {
            if (!$skip_html) {
                  print "<p>";
                  printf (_("SIP settings have been sent to %s"), $this->email);
            }
            if ($_REQUEST['password_reset'] == 'on') {
                $this->sendPasswordReset($skip_html);
            }
            return 1;
        }
    }

    function sendChangedEmail($skip_html=False, $fields=array()) {
        dprint ("SipSettings->sendChangedEmail($this->email)");
        //dprint_r($fields);
        $this->ip = $_SERVER['REMOTE_ADDR'];
        if (!$this->email && !$skip_html) {
            print "<p><font color=blue>";
            print _("Please fill in the e-mail address. ");
            print "</font>";
            return false;
        }

        //$this->location = "Unknown";
        $_loc=geoip_record_by_name($_SERVER['REMOTE_ADDR']);
        if ($_loc['country_name']) {
            $this->location = $_loc['country_name'];
        }
        $subject = sprintf("SIP Account %s changed",$this->account);


        $tpl = $this->getChangedEmailTemplate($this->reseller, $this->Preferences['language']);

        if (!$tpl && !$skip_html) {
            print "<p><font color=red>";
            print _("Error: no email template found");
            print "</font>";
            return false;
        }

        $tpl_html = $this->getChangedEmailTemplateHTML($this->reseller, $this->Preferences['language']);

        define("SMARTY_DIR", "/usr/share/php/smarty/libs/");
        include_once(SMARTY_DIR . 'Smarty.class.php');

        $smarty = new Smarty;
        $smarty->template_dir = '.';

        //$smarty->use_sub_dirs = true;
        //$smarty->cache_dir = 'templates_c';
        $this->fields = $fields;
        $smarty->assign('client', $this);
        $bodyt = $smarty->fetch($tpl);

        if ($tpl_html) {
            $bodyhtml = $smarty->fetch($tpl_html);
        }

        include_once 'Mail.php';
        include_once 'Mail/mime.php' ;

        $hdrs = array(
            'From'    => $this->support_email,
            'Subject' => $subject
        );

        $crlf = "\n";
        $mime = new Mail_mime($crlf);

        $mime->setTXTBody($bodyt);

        if ($tpl_html) {
          $mime->setHTMLBody($bodyhtml);
        }

        $body = $mime->get();
        $hdrs = $mime->headers($hdrs);

        $mail =& Mail::factory('mail');

        //dprint_r($_REQUEST);

        if ($mail->send($this->email, $hdrs, $body)) {
            return 1;
        }
    }

    function sendRemoveAccount() {

        $this->ip = $_SERVER['REMOTE_ADDR'];
        $subject=sprintf ("The account %s was removed from IP Address: %s",$this->account, $this->ip);

        syslog(LOG_NOTICE, $subject);

    }

    function sendPasswordReset($skip_html=False) {
        dprint ("SipSettings->sendPasswordEmail($this->email)");

        $identifier = RandomIdentifier();
        $this->db = new DB_CDRTool();

        $this->ip = $_SERVER['REMOTE_ADDR'];
        $insert_data = array (
            'sip_account'   => $this->account,
            'email'         => $this->email,
            'ip'            => $this->ip
        );

        $this->expire=date("Y-m-d H:i:s",strtotime("+30 minutes"));
        $query=sprintf("insert into memcache set `key`='email_%s', `value`='%s', `expire`='%s'",
        $identifier,
        json_encode($insert_data),
        $this->expire );

        $this->db->query($query);
        $this->identifier = $identifier;

        dprint("$query <br> Identifier: $identifier");

        if (!$this->email && !$skip_html) {
            print "<p><font color=blue>";
            print _("Please fill in the e-mail address. ");
            print "</font>";
            return false;
        }

        $subject = sprintf("Password reset for %s",$this->account);

        $tpl_html = $this->getEmailPasswordTemplateHTML($this->reseller, $this->Preferences['language']);

        define("SMARTY_DIR", "/usr/share/php/smarty/libs/");
        include_once(SMARTY_DIR . 'Smarty.class.php');

        $smarty = new Smarty;
        $smarty->template_dir = '.';

        //$smarty->use_sub_dirs = true;
        //$smarty->cache_dir = 'templates_c';

        $smarty->assign('client', $this);

        $bodyhtml = $smarty->fetch($tpl_html);

        include_once 'Mail.php';
        include_once 'Mail/mime.php' ;

        $hdrs = array(
            'From'    => $this->support_email,
            'Subject' => $subject
        );

        $crlf = "\n";
        $mime = new Mail_mime($crlf);

        $mime->setHTMLBody($bodyhtml);

        $body = $mime->get();
        $hdrs = $mime->headers($hdrs);

        $mail =& Mail::factory('mail');

        //dprint_r($_REQUEST);

        if ($mail->send($this->email, $hdrs, $body) && !$skip_html) {
            print "<li>";
            printf (_("Password reset has been sent to %s"), $this->email);
            print "</li>";
        }
        return 1;
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
           <div class=row-fluid><div class=span12><h4>";
            print $chapter;
            print "
          </h4></div></div>
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
            $datePrint="<span class=\"label label-success\">".sprintf(_("Today"))."</span> ".$time;
        } else if ($date== Date("Y-m-d",time()-3600*24)) {
            $datePrint="<span class=\"label label-info\">".sprintf(_("Yesterday"))."</span> ".$time;
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

    function getEmailTemplate($reseller, $language='en') {
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

    function getEmailTemplateHTML($reseller, $language='en') {
        $file = "sip_settings_email_$language.html.tpl";
        $file2 = "sip_settings_email.html.tpl";

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

    function getChangedEmailTemplate($reseller, $language='en') {
        $file = "sip_settings_changed_$language.tpl";
        $file2 = "sip_settings_changed.tpl";

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

    function getChangedEmailTemplateHTML($reseller, $language='en') {
        $file = "sip_settings_changed_$language.html.tpl";
        $file2 = "sip_settings_changed.html.tpl";

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

    function getEmailPasswordTemplateHTML($reseller, $language='en') {
        $file = "password_reminder_$language.html.tpl";
        $file2 = "password_reminder.html.tpl";

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

    function getEmailDeleteTemplateHTML($reseller, $language='en') {
        $file = "delete_$language.html.tpl";
        $file2 = "delete.html.tpl";

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
        $this->RatingPort->addHeader($this->SoapAuthRating);
        $result     = $this->RatingPort->getEntityProfiles("subscriber://".$this->account);

        if ((new PEAR)->isError($result)) {
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
        <div class='control-group even'>
          <label for=profileWeekday class=control-label>";
            print _("Weekdays");
            printf ("
          </label>
          <div class=controls>
          <div class=input-append>
          <input class=input-medium type=text size=10 maxsize=64 name=profileWeekday value='%s'><span class=add-on>/</span><input class=input-medium type=text size=10 maxsize=64 name=profileWeekdayAlt value='%s'>
            ",
            $this->billingProfiles->profileWeekday,
            $this->billingProfiles->profileWeekdayAlt
            );

            print "
                </div>
          </div>
        </div>
        ";

        print "
        <div class='control-group odd'>
          <label for=profileWeekend class=control-label>";
            print _("Weekends");
            printf ("
          </label>
          <div class=controls>
          <div class=input-append>
          <input class=input-medium type=text size=10 maxsize=64 name=profileWeekend value='%s'><span class=add-on>/</span><input class=input-medium type=text size=10 maxsize=64 name=profileWeekendAlt value='%s'>
            ",
            $this->billingProfiles->profileWeekend,
            $this->billingProfiles->profileWeekendAlt
            );

            print "
                </div>
          </div>
        </div>
        ";

        print "
        <div class='control-group even'>
          <label for=profileTimezone class=control-label>";
            print _("Timezone");
            print ("
          </label>
          <div class=controls>
            ");

            if ($this->billingProfiles->timezone) {
                $_timezone=$this->billingProfiles->timezone;
            } else {
                $_timezone=$this->resellerProperties['timezone'];
            }

            $this->showTimezones('profileTimezone',$_timezone);

            print "
          </div>
        </div>
        ";

    }

    function updateBillingProfiles() {
        if ($this->login_type != 'reseller' && $this->login_type != 'admin') {
            return false;
        }

        if (!$this->pstn_changes_allowed) {
            return true;
        }

        $this->RatingPort->addHeader($this->SoapAuthRating);
        $result     = $this->RatingPort->getEntityProfiles("subscriber://".$this->account);

        if ((new PEAR)->isError($result)) {
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

        $this->RatingPort->addHeader($this->SoapAuthRating);
        if ($this->billingProfiles->profileWeekday && !$profiles['profileWeekday']) {
            // delete profile
            $result     = $this->RatingPort->deleteEntityProfiles('subscriber://'.$this->account);
            if ((new PEAR)->isError($result)) {
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

            if ((new PEAR)->isError($result)) {
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
            <div class=control-group>
            <label for=extra_groups class=control-label>";
            print _("Extra Groups");
            print "
            </label>
            <div class=controls>";
            printf ("<input class=input-medium type=text size=30 name=extra_groups value='%s'>",trim($extraGroups_text));
            print "
            </div>
            </div>
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

        if (!$this->owner) {
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
        "commonName"             => $this->owner,
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
        if (!$this->owner) return;
        Header("Content-type: application/x-crt");
        $header=sprintf("Content-Disposition: inline; filename=sipthor-owner-certificate-%s.crt",$this->owner);
	Header($header);
        $cert=$this->generateCertificate();
        $crt=$cert['crt'].$cert['key'];
        print $crt;
    }

    function exportCertificateP12() {
        if (!$this->owner) return;
        $cert=$this->generateCertificate();
        Header("Content-type: application/x-p12");
        $header=sprintf("Content-Disposition: inline; filename=sipthor-owner-certificate-%s.p12",$this->owner);
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

        <form class='form-inline' action=$this->url method=post>
        <input type=hidden name=tab value='contacts'>
        ";
        print $this->hiddenElements;
        print "<div class=control-group>";
        print "<input type=text size=20 name='firstname' placeholder='";
        print _("First Name");
        printf ("' value='%s'> ",$_REQUEST['firstname']);

        print "<div class=input-append>";
        print "<input type=text size=20 name='lastname' placeholder='";
        print _("Last Name");
        printf ("' value='%s'>",$_REQUEST['lastname']);

        print "<button class=btn type=submit>";
        print _("Search");
        print "</button>";
        print "</div></div>
        </form>
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

        if ((new PEAR)->isError($result)) {
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
            <div class=row-fluid>
            <div class=span12 style='text-align:center'>";

            printf(_("%s contacts found. "),$this->rows);

            if ($this->isEmbedded()) {
                //printf (_("Click on %s to add a Contact to Blink. "),$this->plus_sign_img);
            }
            print "</div>
            </div>
            <table class='table table-condensed table-striped' border=0 cellpadding=2 width=100%>
            <thead>
            <tr>
            <td bgcolor=white></td>";

            print "<th><b>";
            print _('Display Name');
            print "</b></th>";
            print "<th><b>";
            print _('SIP Address');
            print "</b></th>";
            print "<th><b>";
            print _('Timezone');
            print "</b></th>";
            print "<th><b>";
            print _('Action');
            print "</b></th>";
            print "
            </tr></thead>
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
                    //$add_contact_url=sprintf("<a href=\"javascript:blink.addContact_withDisplayName_('%s', '%s');\">%s</a>",$sip_account,$name,$this->plus_sign_img);
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

}

function lookupGeoLocation($ip) {
    if ($_loc=geoip_record_by_name($ip)) {
        $_loc['timezone'] = get_time_zone($_loc['country_code'], $_loc['region']);
        $_loc['region'] = get_region($_loc['country_code'], $_loc['region']);

        $country_transition = array(
            "A1" => "N/A",
            "A2" => "N/A",
            "O1" => "N/A",
            "AP" => "N/A",
            "GB" => "UK");

        if (array_key_exists($_loc['country_code'],$country_transition)) {
            $_loc['country_code'] = $country_transition[$_loc['country_code']];
        }

        return $_loc;
    } else {
        return array();
    }
}

function get_region($country, $region) {
    if ($country == "US" || $country =="CA" ) {
        $full_region = $region;
        // If region can't be found make it a default region to prevent NGNpro error
        if ($full_region == '' && $country == "US") {
            $full_region = "NY";
        } else if ($full_region == '' && $country == "CA") {
            $full_region = "QC";
        }
    } else {
        $full_region='';
    }
    return $full_region;
}

function get_time_zone($country, $region) {
      switch ($country) {
    case "US":
        switch ($region) {
      case "AL":
          $timezone = "America/Chicago";
          break;
      case "AK":
          $timezone = "America/Anchorage";
          break;
      case "AZ":
          $timezone = "America/Phoenix";
          break;
      case "AR":
          $timezone = "America/Chicago";
          break;
      case "CA":
          $timezone = "America/Los_Angeles";
          break;
      case "CO":
          $timezone = "America/Denver";
          break;
      case "CT":
          $timezone = "America/New_York";
          break;
      case "DE":
          $timezone = "America/New_York";
          break;
      case "DC":
          $timezone = "America/New_York";
          break;
      case "FL":
          $timezone = "America/New_York";
          break;
      case "GA":
          $timezone = "America/New_York";
          break;
      case "HI":
          $timezone = "Pacific/Honolulu";
          break;
      case "ID":
          $timezone = "America/Denver";
          break;
      case "IL":
          $timezone = "America/Chicago";
          break;
      case "IN":
          $timezone = "America/Indianapolis";
          break;
      case "IA":
          $timezone = "America/Chicago";
          break;
      case "KS":
          $timezone = "America/Chicago";
          break;
      case "KY":
          $timezone = "America/New_York";
          break;
      case "LA":
          $timezone = "America/Chicago";
          break;
      case "ME":
          $timezone = "America/New_York";
          break;
      case "MD":
          $timezone = "America/New_York";
          break;
      case "MA":
          $timezone = "America/New_York";
          break;
      case "MI":
          $timezone = "America/New_York";
          break;
      case "MN":
          $timezone = "America/Chicago";
          break;
      case "MS":
          $timezone = "America/Chicago";
          break;
      case "MO":
          $timezone = "America/Chicago";
          break;
      case "MT":
          $timezone = "America/Denver";
          break;
      case "NE":
          $timezone = "America/Chicago";
          break;
      case "NV":
          $timezone = "America/Los_Angeles";
          break;
      case "NH":
          $timezone = "America/New_York";
          break;
      case "NJ":
          $timezone = "America/New_York";
          break;
      case "NM":
          $timezone = "America/Denver";
          break;
      case "NY":
          $timezone = "America/New_York";
          break;
      case "NC":
          $timezone = "America/New_York";
          break;
      case "ND":
          $timezone = "America/Chicago";
          break;
      case "OH":
          $timezone = "America/New_York";
          break;
      case "OK":
          $timezone = "America/Chicago";
          break;
      case "OR":
          $timezone = "America/Los_Angeles";
          break;
      case "PA":
          $timezone = "America/New_York";
          break;
      case "RI":
          $timezone = "America/New_York";
          break;
      case "SC":
          $timezone = "America/New_York";
          break;
      case "SD":
          $timezone = "America/Chicago";
          break;
      case "TN":
          $timezone = "America/Chicago";
          break;
      case "TX":
          $timezone = "America/Chicago";
          break;
      case "UT":
          $timezone = "America/Denver";
          break;
      case "VT":
          $timezone = "America/New_York";
          break;
      case "VA":
          $timezone = "America/New_York";
          break;
      case "WA":
          $timezone = "America/Los_Angeles";
          break;
      case "WV":
          $timezone = "America/New_York";
          break;
      case "WI":
          $timezone = "America/Chicago";
          break;
      case "WY":
          $timezone = "America/Denver";
          break;
      }
      break;
    case "CA":
        switch ($region) {
      case "AB":
          $timezone = "America/Edmonton";
          break;
      case "BC":
          $timezone = "America/Vancouver";
          break;
      case "MB":
          $timezone = "America/Winnipeg";
          break;
      case "NB":
          $timezone = "America/Halifax";
          break;
      case "NL":
          $timezone = "America/St_Johns";
          break;
      case "NT":
          $timezone = "America/Yellowknife";
          break;
      case "NS":
          $timezone = "America/Halifax";
          break;
      case "NU":
          $timezone = "America/Rankin_Inlet";
          break;
      case "ON":
          $timezone = "America/Rainy_River";
          break;
      case "PE":
          $timezone = "America/Halifax";
          break;
      case "QC":
          $timezone = "America/Montreal";
          break;
      case "SK":
          $timezone = "America/Regina";
          break;
      case "YT":
          $timezone = "America/Whitehorse";
          break;
      }
      break;
    case "AU":
        switch ($region) {
      case "01":
          $timezone = "Australia/Canberra";
          break;
      case "02":
          $timezone = "Australia/NSW";
          break;
      case "03":
          $timezone = "Australia/North";
          break;
      case "04":
          $timezone = "Australia/Queensland";
          break;
      case "05":
          $timezone = "Australia/South";
          break;
      case "06":
          $timezone = "Australia/Tasmania";
          break;
      case "07":
          $timezone = "Australia/Victoria";
          break;
      case "08":
          $timezone = "Australia/West";
          break;
      }
      break;
    case "AS":
        $timezone = "US/Samoa";
        break;
    case "CI":
        $timezone = "Africa/Abidjan";
        break;
    case "GH":
        $timezone = "Africa/Accra";
        break;
    case "DZ":
        $timezone = "Africa/Algiers";
        break;
    case "ER":
        $timezone = "Africa/Asmera";
        break;
    case "ML":
        $timezone = "Africa/Bamako";
        break;
    case "CF":
        $timezone = "Africa/Bangui";
        break;
    case "GM":
        $timezone = "Africa/Banjul";
        break;
    case "GW":
        $timezone = "Africa/Bissau";
        break;
    case "CG":
        $timezone = "Africa/Brazzaville";
        break;
    case "BI":
        $timezone = "Africa/Bujumbura";
        break;
    case "EG":
        $timezone = "Africa/Cairo";
        break;
    case "MA":
        $timezone = "Africa/Casablanca";
        break;
    case "GN":
        $timezone = "Africa/Conakry";
        break;
    case "SN":
        $timezone = "Africa/Dakar";
        break;
    case "DJ":
        $timezone = "Africa/Djibouti";
        break;
    case "SL":
        $timezone = "Africa/Freetown";
        break;
    case "BW":
        $timezone = "Africa/Gaborone";
        break;
    case "ZW":
        $timezone = "Africa/Harare";
        break;
    case "ZA":
        $timezone = "Africa/Johannesburg";
        break;
    case "UG":
        $timezone = "Africa/Kampala";
        break;
    case "SD":
        $timezone = "Africa/Khartoum";
        break;
    case "RW":
        $timezone = "Africa/Kigali";
        break;
    case "NG":
        $timezone = "Africa/Lagos";
        break;
    case "GA":
        $timezone = "Africa/Libreville";
        break;
    case "TG":
        $timezone = "Africa/Lome";
        break;
    case "AO":
        $timezone = "Africa/Luanda";
        break;
    case "ZM":
        $timezone = "Africa/Lusaka";
        break;
    case "GQ":
        $timezone = "Africa/Malabo";
        break;
    case "MZ":
        $timezone = "Africa/Maputo";
        break;
    case "LS":
        $timezone = "Africa/Maseru";
        break;
    case "SZ":
        $timezone = "Africa/Mbabane";
        break;
    case "SO":
        $timezone = "Africa/Mogadishu";
        break;
    case "LR":
        $timezone = "Africa/Monrovia";
        break;
    case "KE":
        $timezone = "Africa/Nairobi";
        break;
    case "TD":
        $timezone = "Africa/Ndjamena";
        break;
    case "NE":
        $timezone = "Africa/Niamey";
        break;
    case "MR":
        $timezone = "Africa/Nouakchott";
        break;
    case "BF":
        $timezone = "Africa/Ouagadougou";
        break;
    case "ST":
        $timezone = "Africa/Sao_Tome";
        break;
    case "LY":
        $timezone = "Africa/Tripoli";
        break;
    case "TN":
        $timezone = "Africa/Tunis";
        break;
    case "AI":
        $timezone = "America/Anguilla";
        break;
    case "AG":
        $timezone = "America/Antigua";
        break;
    case "AW":
        $timezone = "America/Aruba";
        break;
    case "BB":
        $timezone = "America/Barbados";
        break;
    case "BZ":
        $timezone = "America/Belize";
        break;
    case "CO":
        $timezone = "America/Bogota";
        break;
    case "VE":
        $timezone = "America/Caracas";
        break;
    case "KY":
        $timezone = "America/Cayman";
        break;
    case "CR":
        $timezone = "America/Costa_Rica";
        break;
    case "DM":
        $timezone = "America/Dominica";
        break;
    case "SV":
        $timezone = "America/El_Salvador";
        break;
    case "GD":
        $timezone = "America/Grenada";
        break;
    case "FR":
        $timezone = "Europe/Paris";
        break;
    case "GP":
        $timezone = "America/Guadeloupe";
        break;
    case "GT":
        $timezone = "America/Guatemala";
        break;
    case "GY":
        $timezone = "America/Guyana";
        break;
    case "CU":
        $timezone = "America/Havana";
        break;
    case "JM":
        $timezone = "America/Jamaica";
        break;
    case "BO":
        $timezone = "America/La_Paz";
        break;
    case "PE":
        $timezone = "America/Lima";
        break;
    case "NI":
        $timezone = "America/Managua";
        break;
    case "MQ":
        $timezone = "America/Martinique";
        break;
    case "UY":
        $timezone = "America/Montevideo";
        break;
    case "MS":
        $timezone = "America/Montserrat";
        break;
    case "BS":
        $timezone = "America/Nassau";
        break;
    case "PA":
        $timezone = "America/Panama";
        break;
    case "SR":
        $timezone = "America/Paramaribo";
        break;
    case "PR":
        $timezone = "America/Puerto_Rico";
        break;
    case "KN":
        $timezone = "America/St_Kitts";
        break;
    case "LC":
        $timezone = "America/St_Lucia";
        break;
    case "VC":
        $timezone = "America/St_Vincent";
        break;
    case "HN":
        $timezone = "America/Tegucigalpa";
        break;
    case "YE":
        $timezone = "Asia/Aden";
        break;
    case "JO":
        $timezone = "Asia/Amman";
        break;
    case "TM":
        $timezone = "Asia/Ashgabat";
        break;
    case "IQ":
        $timezone = "Asia/Baghdad";
        break;
    case "BH":
        $timezone = "Asia/Bahrain";
        break;
    case "AZ":
        $timezone = "Asia/Baku";
        break;
    case "TH":
        $timezone = "Asia/Bangkok";
        break;
    case "LB":
        $timezone = "Asia/Beirut";
        break;
    case "KG":
        $timezone = "Asia/Bishkek";
        break;
    case "BN":
        $timezone = "Asia/Brunei";
        break;
    case "IN":
        $timezone = "Asia/Calcutta";
        break;
    case "MN":
        $timezone = "Asia/Choibalsan";
        break;
    case "LK":
        $timezone = "Asia/Colombo";
        break;
    case "BD":
        $timezone = "Asia/Dhaka";
        break;
    case "AE":
        $timezone = "Asia/Dubai";
        break;
    case "TJ":
        $timezone = "Asia/Dushanbe";
        break;
    case "HK":
        $timezone = "Asia/Hong_Kong";
        break;
    case "TR":
        $timezone = "Asia/Istanbul";
        break;
    case "IL":
        $timezone = "Asia/Jerusalem";
        break;
    case "AF":
        $timezone = "Asia/Kabul";
        break;
    case "PK":
        $timezone = "Asia/Karachi";
        break;
    case "NP":
        $timezone = "Asia/Katmandu";
        break;
    case "KW":
        $timezone = "Asia/Kuwait";
        break;
    case "MO":
        $timezone = "Asia/Macao";
        break;
    case "PH":
        $timezone = "Asia/Manila";
        break;
    case "OM":
        $timezone = "Asia/Muscat";
        break;
    case "CY":
        $timezone = "Asia/Nicosia";
        break;
    case "KP":
        $timezone = "Asia/Pyongyang";
        break;
    case "QA":
        $timezone = "Asia/Qatar";
        break;
    case "MM":
        $timezone = "Asia/Rangoon";
        break;
    case "SA":
        $timezone = "Asia/Riyadh";
        break;
    case "KR":
        $timezone = "Asia/Seoul";
        break;
    case "SG":
        $timezone = "Asia/Singapore";
        break;
    case "TW":
        $timezone = "Asia/Taipei";
        break;
    case "GE":
        $timezone = "Asia/Tbilisi";
        break;
    case "BT":
        $timezone = "Asia/Thimphu";
        break;
    case "JP":
        $timezone = "Asia/Tokyo";
        break;
    case "LA":
        $timezone = "Asia/Vientiane";
        break;
    case "AM":
        $timezone = "Asia/Yerevan";
        break;
    case "BM":
        $timezone = "Atlantic/Bermuda";
        break;
    case "CV":
        $timezone = "Atlantic/Cape_Verde";
        break;
    case "FO":
        $timezone = "Atlantic/Faeroe";
        break;
    case "IS":
        $timezone = "Atlantic/Reykjavik";
        break;
    case "GS":
        $timezone = "Atlantic/South_Georgia";
        break;
    case "SH":
        $timezone = "Atlantic/St_Helena";
        break;
    case "CL":
        $timezone = "Chile/Continental";
        break;
    case "NL":
        $timezone = "Europe/Amsterdam";
        break;
    case "AD":
        $timezone = "Europe/Andorra";
        break;
    case "GR":
        $timezone = "Europe/Athens";
        break;
    case "YU":
        $timezone = "Europe/Belgrade";
        break;
    case "DE":
        $timezone = "Europe/Berlin";
        break;
    case "SK":
        $timezone = "Europe/Bratislava";
        break;
    case "BE":
        $timezone = "Europe/Brussels";
        break;
    case "RO":
        $timezone = "Europe/Bucharest";
        break;
    case "HU":
        $timezone = "Europe/Budapest";
        break;
    case "DK":
        $timezone = "Europe/Copenhagen";
        break;
    case "IE":
        $timezone = "Europe/Dublin";
        break;
    case "GI":
        $timezone = "Europe/Gibraltar";
        break;
    case "FI":
        $timezone = "Europe/Helsinki";
        break;
    case "SI":
        $timezone = "Europe/Ljubljana";
        break;
    case "GB":
        $timezone = "Europe/London";
        break;
    case "LU":
        $timezone = "Europe/Luxembourg";
        break;
    case "MT":
        $timezone = "Europe/Malta";
        break;
    case "BY":
        $timezone = "Europe/Minsk";
        break;
    case "MC":
        $timezone = "Europe/Monaco";
        break;
    case "NO":
        $timezone = "Europe/Oslo";
        break;
    case "CZ":
        $timezone = "Europe/Prague";
        break;
    case "LV":
        $timezone = "Europe/Riga";
        break;
    case "IT":
        $timezone = "Europe/Rome";
        break;
    case "SM":
        $timezone = "Europe/San_Marino";
        break;
    case "BA":
        $timezone = "Europe/Sarajevo";
        break;
    case "MK":
        $timezone = "Europe/Skopje";
        break;
    case "BG":
        $timezone = "Europe/Sofia";
        break;
    case "SE":
        $timezone = "Europe/Stockholm";
        break;
    case "EE":
        $timezone = "Europe/Tallinn";
        break;
    case "AL":
        $timezone = "Europe/Tirane";
        break;
    case "LI":
        $timezone = "Europe/Vaduz";
        break;
    case "VA":
        $timezone = "Europe/Vatican";
        break;
    case "AT":
        $timezone = "Europe/Vienna";
        break;
    case "LT":
        $timezone = "Europe/Vilnius";
        break;
    case "PL":
        $timezone = "Europe/Warsaw";
        break;
    case "HR":
        $timezone = "Europe/Zagreb";
        break;
    case "IR":
        $timezone = "Asia/Tehran";
        break;
    case "MG":
        $timezone = "Indian/Antananarivo";
        break;
    case "CX":
        $timezone = "Indian/Christmas";
        break;
    case "CC":
        $timezone = "Indian/Cocos";
        break;
    case "KM":
        $timezone = "Indian/Comoro";
        break;
    case "MV":
        $timezone = "Indian/Maldives";
        break;
    case "MU":
        $timezone = "Indian/Mauritius";
        break;
    case "YT":
        $timezone = "Indian/Mayotte";
        break;
    case "RE":
        $timezone = "Indian/Reunion";
        break;
    case "FJ":
        $timezone = "Pacific/Fiji";
        break;
    case "TV":
        $timezone = "Pacific/Funafuti";
        break;
    case "GU":
        $timezone = "Pacific/Guam";
        break;
    case "NR":
        $timezone = "Pacific/Nauru";
        break;
    case "NU":
        $timezone = "Pacific/Niue";
        break;
    case "NF":
        $timezone = "Pacific/Norfolk";
        break;
    case "PW":
        $timezone = "Pacific/Palau";
        break;
    case "PN":
        $timezone = "Pacific/Pitcairn";
        break;
    case "CK":
        $timezone = "Pacific/Rarotonga";
        break;
    case "WS":
        $timezone = "Pacific/Samoa";
        break;
    case "KI":
        $timezone = "Pacific/Tarawa";
        break;
    case "TO":
        $timezone = "Pacific/Tongatapu";
        break;
    case "WF":
        $timezone = "Pacific/Wallis";
        break;
    case "TZ":
        $timezone = "Africa/Dar_es_Salaam";
        break;
    case "VN":
        $timezone = "Asia/Phnom_Penh";
        break;
    case "KH":
        $timezone = "Asia/Phnom_Penh";
        break;
    case "CM":
        $timezone = "Africa/Lagos";
        break;
    case "DO":
        $timezone = "America/Santo_Domingo";
        break;
    case "ET":
        $timezone = "Africa/Addis_Ababa";
        break;
    case "FX":
        $timezone = "Europe/Paris";
        break;
    case "HT":
        $timezone = "America/Port-au-Prince";
        break;
    case "CH":
        $timezone = "Europe/Zurich";
        break;
    case "AN":
        $timezone = "America/Curacao";
        break;
    case "BJ":
        $timezone = "Africa/Porto-Novo";
        break;
    case "EH":
        $timezone = "Africa/El_Aaiun";
        break;
    case "FK":
        $timezone = "Atlantic/Stanley";
        break;
    case "GF":
        $timezone = "America/Cayenne";
        break;
    case "IO":
        $timezone = "Indian/Chagos";
        break;
    case "MD":
        $timezone = "Europe/Chisinau";
        break;
    case "MP":
        $timezone = "Pacific/Saipan";
        break;
    case "MW":
        $timezone = "Africa/Blantyre";
        break;
    case "NA":
        $timezone = "Africa/Windhoek";
        break;
    case "NC":
        $timezone = "Pacific/Noumea";
        break;
    case "PG":
        $timezone = "Pacific/Port_Moresby";
        break;
    case "PM":
        $timezone = "America/Miquelon";
        break;
    case "PS":
        $timezone = "Asia/Gaza";
        break;
    case "PY":
        $timezone = "America/Asuncion";
        break;
    case "SB":
        $timezone = "Pacific/Guadalcanal";
        break;
    case "SC":
        $timezone = "Indian/Mahe";
        break;
    case "SJ":
        $timezone = "Arctic/Longyearbyen";
        break;
    case "SY":
        $timezone = "Asia/Damascus";
        break;
    case "TC":
        $timezone = "America/Grand_Turk";
        break;
    case "TF":
        $timezone = "Indian/Kerguelen";
        break;
    case "TK":
        $timezone = "Pacific/Fakaofo";
        break;
    case "TT":
        $timezone = "America/Port_of_Spain";
        break;
    case "VG":
        $timezone = "America/Tortola";
        break;
    case "VI":
        $timezone = "America/St_Thomas";
        break;
    case "VU":
        $timezone = "Pacific/Efate";
        break;
    case "RS":
        $timezone = "Europe/Belgrade";
        break;
    case "ME":
        $timezone = "Europe/Podgorica";
        break;
    case "AX":
        $timezone = "Europe/Mariehamn";
        break;
    case "GG":
        $timezone = "Europe/Guernsey";
        break;
    case "IM":
        $timezone = "Europe/Isle_of_Man";
        break;
    case "JE":
        $timezone = "Europe/Jersey";
        break;
    case "BL":
        $timezone = "America/St_Barthelemy";
        break;
    case "MF":
        $timezone = "America/Marigot";
        break;
    case "AR":
        switch ($region) {
      case "01":
          $timezone = "America/Argentina/Buenos_Aires";
          break;
      case "02":
          $timezone = "America/Argentina/Catamarca";
          break;
      case "03":
          $timezone = "America/Argentina/Tucuman";
          break;
      case "04":
          $timezone = "America/Argentina/Rio_Gallegos";
          break;
      case "05":
          $timezone = "America/Argentina/Cordoba";
          break;
      case "06":
          $timezone = "America/Argentina/Tucuman";
          break;
      case "07":
          $timezone = "America/Argentina/Buenos_Aires";
          break;
      case "08":
          $timezone = "America/Argentina/Buenos_Aires";
          break;
      case "09":
          $timezone = "America/Argentina/Tucuman";
          break;
      case "10":
          $timezone = "America/Argentina/Jujuy";
          break;
      case "11":
          $timezone = "America/Argentina/San_Luis";
          break;
      case "12":
          $timezone = "America/Argentina/La_Rioja";
          break;
      case "13":
          $timezone = "America/Argentina/Mendoza";
          break;
      case "14":
          $timezone = "America/Argentina/Buenos_Aires";
          break;
      case "15":
          $timezone = "America/Argentina/San_Luis";
          break;
      case "16":
          $timezone = "America/Argentina/Buenos_Aires";
          break;
      case "17":
          $timezone = "America/Argentina/Salta";
          break;
      case "18":
          $timezone = "America/Argentina/San_Juan";
          break;
      case "19":
          $timezone = "America/Argentina/San_Luis";
          break;
      case "20":
          $timezone = "America/Argentina/Rio_Gallegos";
          break;
      case "21":
          $timezone = "America/Argentina/Buenos_Aires";
          break;
      case "22":
          $timezone = "America/Argentina/Catamarca";
          break;
      case "23":
          $timezone = "America/Argentina/Ushuaia";
          break;
      case "24":
          $timezone = "America/Argentina/Tucuman";
          break;
      }
      break;
    case "BR":
        switch ($region) {
      case "01":
          $timezone = "America/Rio_Branco";
          break;
      case "02":
          $timezone = "America/Maceio";
          break;
      case "03":
          $timezone = "America/Sao_Paulo";
          break;
      case "04":
          $timezone = "America/Manaus";
          break;
      case "05":
          $timezone = "America/Bahia";
          break;
      case "06":
          $timezone = "America/Fortaleza";
          break;
      case "07":
          $timezone = "America/Sao_Paulo";
          break;
      case "08":
          $timezone = "America/Sao_Paulo";
          break;
      case "11":
          $timezone = "America/Campo_Grande";
          break;
      case "13":
          $timezone = "America/Belem";
          break;
      case "14":
          $timezone = "America/Cuiaba";
          break;
      case "15":
          $timezone = "America/Sao_Paulo";
          break;
      case "16":
          $timezone = "America/Belem";
          break;
      case "17":
          $timezone = "America/Recife";
          break;
      case "18":
          $timezone = "America/Sao_Paulo";
          break;
      case "20":
          $timezone = "America/Fortaleza";
          break;
      case "21":
          $timezone = "America/Sao_Paulo";
          break;
      case "22":
          $timezone = "America/Recife";
          break;
      case "23":
          $timezone = "America/Sao_Paulo";
          break;
      case "24":
          $timezone = "America/Porto_Velho";
          break;
      case "25":
          $timezone = "America/Boa_Vista";
          break;
      case "26":
          $timezone = "America/Sao_Paulo";
          break;
      case "27":
          $timezone = "America/Sao_Paulo";
          break;
      case "28":
          $timezone = "America/Maceio";
          break;
      case "29":
          $timezone = "America/Sao_Paulo";
          break;
      case "30":
          $timezone = "America/Recife";
          break;
      case "31":
          $timezone = "America/Araguaina";
          break;
      }
      break;
    case "CD":
        switch ($region) {
      case "02":
          $timezone = "Africa/Kinshasa";
          break;
      case "05":
          $timezone = "Africa/Lubumbashi";
          break;
      case "06":
          $timezone = "Africa/Kinshasa";
          break;
      case "08":
          $timezone = "Africa/Kinshasa";
          break;
      case "10":
          $timezone = "Africa/Lubumbashi";
          break;
      case "11":
          $timezone = "Africa/Lubumbashi";
          break;
      case "12":
          $timezone = "Africa/Lubumbashi";
          break;
      }
      break;
    case "CN":
        switch ($region) {
      case "01":
          $timezone = "Asia/Shanghai";
          break;
      case "02":
          $timezone = "Asia/Shanghai";
          break;
      case "03":
          $timezone = "Asia/Shanghai";
          break;
      case "04":
          $timezone = "Asia/Shanghai";
          break;
      case "05":
          $timezone = "Asia/Harbin";
          break;
      case "06":
          $timezone = "Asia/Chongqing";
          break;
      case "07":
          $timezone = "Asia/Shanghai";
          break;
      case "08":
          $timezone = "Asia/Harbin";
          break;
      case "09":
          $timezone = "Asia/Shanghai";
          break;
      case "10":
          $timezone = "Asia/Shanghai";
          break;
      case "11":
          $timezone = "Asia/Chongqing";
          break;
      case "12":
          $timezone = "Asia/Shanghai";
          break;
      case "13":
          $timezone = "Asia/Urumqi";
          break;
      case "14":
          $timezone = "Asia/Chongqing";
          break;
      case "15":
          $timezone = "Asia/Chongqing";
          break;
      case "16":
          $timezone = "Asia/Chongqing";
          break;
      case "18":
          $timezone = "Asia/Chongqing";
          break;
      case "19":
          $timezone = "Asia/Harbin";
          break;
      case "20":
          $timezone = "Asia/Harbin";
          break;
      case "21":
          $timezone = "Asia/Chongqing";
          break;
      case "22":
          $timezone = "Asia/Harbin";
          break;
      case "23":
          $timezone = "Asia/Shanghai";
          break;
      case "24":
          $timezone = "Asia/Chongqing";
          break;
      case "25":
          $timezone = "Asia/Shanghai";
          break;
      case "26":
          $timezone = "Asia/Chongqing";
          break;
      case "28":
          $timezone = "Asia/Shanghai";
          break;
      case "29":
          $timezone = "Asia/Chongqing";
          break;
      case "30":
          $timezone = "Asia/Chongqing";
          break;
      case "31":
          $timezone = "Asia/Chongqing";
          break;
      case "32":
          $timezone = "Asia/Chongqing";
          break;
      case "33":
          $timezone = "Asia/Chongqing";
          break;
      }
      break;
    case "EC":
        switch ($region) {
      case "01":
          $timezone = "Pacific/Galapagos";
          break;
      case "02":
          $timezone = "America/Guayaquil";
          break;
      case "03":
          $timezone = "America/Guayaquil";
          break;
      case "04":
          $timezone = "America/Guayaquil";
          break;
      case "05":
          $timezone = "America/Guayaquil";
          break;
      case "06":
          $timezone = "America/Guayaquil";
          break;
      case "07":
          $timezone = "America/Guayaquil";
          break;
      case "08":
          $timezone = "America/Guayaquil";
          break;
      case "09":
          $timezone = "America/Guayaquil";
          break;
      case "10":
          $timezone = "America/Guayaquil";
          break;
      case "11":
          $timezone = "America/Guayaquil";
          break;
      case "12":
          $timezone = "America/Guayaquil";
          break;
      case "13":
          $timezone = "America/Guayaquil";
          break;
      case "14":
          $timezone = "America/Guayaquil";
          break;
      case "15":
          $timezone = "America/Guayaquil";
          break;
      case "17":
          $timezone = "America/Guayaquil";
          break;
      case "18":
          $timezone = "America/Guayaquil";
          break;
      case "19":
          $timezone = "America/Guayaquil";
          break;
      case "20":
          $timezone = "America/Guayaquil";
          break;
      case "22":
          $timezone = "America/Guayaquil";
          break;
      }
      break;
    case "ES":
        switch ($region) {
      case "07":
          $timezone = "Europe/Madrid";
          break;
      case "27":
          $timezone = "Europe/Madrid";
          break;
      case "29":
          $timezone = "Europe/Madrid";
          break;
      case "31":
          $timezone = "Europe/Madrid";
          break;
      case "32":
          $timezone = "Europe/Madrid";
          break;
      case "34":
          $timezone = "Europe/Madrid";
          break;
      case "39":
          $timezone = "Europe/Madrid";
          break;
      case "51":
          $timezone = "Africa/Ceuta";
          break;
      case "52":
          $timezone = "Europe/Madrid";
          break;
      case "53":
          $timezone = "Atlantic/Canary";
          break;
      case "54":
          $timezone = "Europe/Madrid";
          break;
      case "55":
          $timezone = "Europe/Madrid";
          break;
      case "56":
          $timezone = "Europe/Madrid";
          break;
      case "57":
          $timezone = "Europe/Madrid";
          break;
      case "58":
          $timezone = "Europe/Madrid";
          break;
      case "59":
          $timezone = "Europe/Madrid";
          break;
      case "60":
          $timezone = "Europe/Madrid";
          break;
      }
      break;
    case "GL":
        switch ($region) {
      case "01":
          $timezone = "America/Thule";
          break;
      case "02":
          $timezone = "America/Godthab";
          break;
      case "03":
          $timezone = "America/Godthab";
          break;
      }
      break;
    case "ID":
        switch ($region) {
      case "01":
          $timezone = "Asia/Pontianak";
          break;
      case "02":
          $timezone = "Asia/Makassar";
          break;
      case "03":
          $timezone = "Asia/Jakarta";
          break;
      case "04":
          $timezone = "Asia/Jakarta";
          break;
      case "05":
          $timezone = "Asia/Jakarta";
          break;
      case "06":
          $timezone = "Asia/Jakarta";
          break;
      case "07":
          $timezone = "Asia/Jakarta";
          break;
      case "08":
          $timezone = "Asia/Jakarta";
          break;
      case "09":
          $timezone = "Asia/Jayapura";
          break;
      case "10":
          $timezone = "Asia/Jakarta";
          break;
      case "11":
          $timezone = "Asia/Pontianak";
          break;
      case "12":
          $timezone = "Asia/Makassar";
          break;
      case "13":
          $timezone = "Asia/Makassar";
          break;
      case "14":
          $timezone = "Asia/Makassar";
          break;
      case "15":
          $timezone = "Asia/Jakarta";
          break;
      case "16":
          $timezone = "Asia/Makassar";
          break;
      case "17":
          $timezone = "Asia/Makassar";
          break;
      case "18":
          $timezone = "Asia/Makassar";
          break;
      case "19":
          $timezone = "Asia/Pontianak";
          break;
      case "20":
          $timezone = "Asia/Makassar";
          break;
      case "21":
          $timezone = "Asia/Makassar";
          break;
      case "22":
          $timezone = "Asia/Makassar";
          break;
      case "23":
          $timezone = "Asia/Makassar";
          break;
      case "24":
          $timezone = "Asia/Jakarta";
          break;
      case "25":
          $timezone = "Asia/Pontianak";
          break;
      case "26":
          $timezone = "Asia/Pontianak";
          break;
      case "30":
          $timezone = "Asia/Jakarta";
          break;
      case "31":
          $timezone = "Asia/Makassar";
          break;
      case "33":
          $timezone = "Asia/Jakarta";
          break;
      }
      break;
    case "KZ":
        switch ($region) {
      case "01":
          $timezone = "Asia/Almaty";
          break;
      case "02":
          $timezone = "Asia/Almaty";
          break;
      case "03":
          $timezone = "Asia/Qyzylorda";
          break;
      case "04":
          $timezone = "Asia/Aqtobe";
          break;
      case "05":
          $timezone = "Asia/Qyzylorda";
          break;
      case "06":
          $timezone = "Asia/Aqtau";
          break;
      case "07":
          $timezone = "Asia/Oral";
          break;
      case "08":
          $timezone = "Asia/Qyzylorda";
          break;
      case "09":
          $timezone = "Asia/Aqtau";
          break;
      case "10":
          $timezone = "Asia/Qyzylorda";
          break;
      case "11":
          $timezone = "Asia/Almaty";
          break;
      case "12":
          $timezone = "Asia/Qyzylorda";
          break;
      case "13":
          $timezone = "Asia/Aqtobe";
          break;
      case "14":
          $timezone = "Asia/Qyzylorda";
          break;
      case "15":
          $timezone = "Asia/Almaty";
          break;
      case "16":
          $timezone = "Asia/Aqtobe";
          break;
      case "17":
          $timezone = "Asia/Almaty";
          break;
      }
      break;
    case "MX":
        switch ($region) {
      case "01":
          $timezone = "America/Mexico_City";
          break;
      case "02":
          $timezone = "America/Tijuana";
          break;
      case "03":
          $timezone = "America/Hermosillo";
          break;
      case "04":
          $timezone = "America/Merida";
          break;
      case "05":
          $timezone = "America/Mexico_City";
          break;
      case "06":
          $timezone = "America/Chihuahua";
          break;
      case "07":
          $timezone = "America/Monterrey";
          break;
      case "08":
          $timezone = "America/Mexico_City";
          break;
      case "09":
          $timezone = "America/Mexico_City";
          break;
      case "10":
          $timezone = "America/Mazatlan";
          break;
      case "11":
          $timezone = "America/Mexico_City";
          break;
      case "12":
          $timezone = "America/Mexico_City";
          break;
      case "13":
          $timezone = "America/Mexico_City";
          break;
      case "14":
          $timezone = "America/Mazatlan";
          break;
      case "15":
          $timezone = "America/Chihuahua";
          break;
      case "16":
          $timezone = "America/Mexico_City";
          break;
      case "17":
          $timezone = "America/Mexico_City";
          break;
      case "18":
          $timezone = "America/Mazatlan";
          break;
      case "19":
          $timezone = "America/Monterrey";
          break;
      case "20":
          $timezone = "America/Mexico_City";
          break;
      case "21":
          $timezone = "America/Mexico_City";
          break;
      case "22":
          $timezone = "America/Mexico_City";
          break;
      case "23":
          $timezone = "America/Cancun";
          break;
      case "24":
          $timezone = "America/Mexico_City";
          break;
      case "25":
          $timezone = "America/Mazatlan";
          break;
      case "26":
          $timezone = "America/Hermosillo";
          break;
      case "27":
          $timezone = "America/Merida";
          break;
      case "28":
          $timezone = "America/Monterrey";
          break;
      case "29":
          $timezone = "America/Mexico_City";
          break;
      case "30":
          $timezone = "America/Mexico_City";
          break;
      case "31":
          $timezone = "America/Merida";
          break;
      case "32":
          $timezone = "America/Monterrey";
          break;
      }
      break;
    case "MY":
        switch ($region) {
      case "01":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "02":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "03":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "04":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "05":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "06":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "07":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "08":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "09":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "11":
          $timezone = "Asia/Kuching";
          break;
      case "12":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "13":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "14":
          $timezone = "Asia/Kuala_Lumpur";
          break;
      case "15":
          $timezone = "Asia/Kuching";
          break;
      case "16":
          $timezone = "Asia/Kuching";
          break;
      }
      break;
    case "NZ":
        switch ($region) {
      case "85":
          $timezone = "Pacific/Auckland";
          break;
      case "E7":
          $timezone = "Pacific/Auckland";
          break;
      case "E8":
          $timezone = "Pacific/Auckland";
          break;
      case "E9":
          $timezone = "Pacific/Auckland";
          break;
      case "F1":
          $timezone = "Pacific/Auckland";
          break;
      case "F2":
          $timezone = "Pacific/Auckland";
          break;
      case "F3":
          $timezone = "Pacific/Auckland";
          break;
      case "F4":
          $timezone = "Pacific/Auckland";
          break;
      case "F5":
          $timezone = "Pacific/Auckland";
          break;
      case "F7":
          $timezone = "Pacific/Chatham";
          break;
      case "F8":
          $timezone = "Pacific/Auckland";
          break;
      case "F9":
          $timezone = "Pacific/Auckland";
          break;
      case "G1":
          $timezone = "Pacific/Auckland";
          break;
      case "G2":
          $timezone = "Pacific/Auckland";
          break;
      case "G3":
          $timezone = "Pacific/Auckland";
          break;
      }
      break;
    case "PT":
        switch ($region) {
      case "02":
          $timezone = "Europe/Lisbon";
          break;
      case "03":
          $timezone = "Europe/Lisbon";
          break;
      case "04":
          $timezone = "Europe/Lisbon";
          break;
      case "05":
          $timezone = "Europe/Lisbon";
          break;
      case "06":
          $timezone = "Europe/Lisbon";
          break;
      case "07":
          $timezone = "Europe/Lisbon";
          break;
      case "08":
          $timezone = "Europe/Lisbon";
          break;
      case "09":
          $timezone = "Europe/Lisbon";
          break;
      case "10":
          $timezone = "Atlantic/Madeira";
          break;
      case "11":
          $timezone = "Europe/Lisbon";
          break;
      case "13":
          $timezone = "Europe/Lisbon";
          break;
      case "14":
          $timezone = "Europe/Lisbon";
          break;
      case "16":
          $timezone = "Europe/Lisbon";
          break;
      case "17":
          $timezone = "Europe/Lisbon";
          break;
      case "18":
          $timezone = "Europe/Lisbon";
          break;
      case "19":
          $timezone = "Europe/Lisbon";
          break;
      case "20":
          $timezone = "Europe/Lisbon";
          break;
      case "21":
          $timezone = "Europe/Lisbon";
          break;
      case "22":
          $timezone = "Europe/Lisbon";
          break;
      }
      break;
    case "RU":
        switch ($region) {
      case "01":
          $timezone = "Europe/Volgograd";
          break;
      case "02":
          $timezone = "Asia/Irkutsk";
          break;
      case "03":
          $timezone = "Asia/Novokuznetsk";
          break;
      case "04":
          $timezone = "Asia/Novosibirsk";
          break;
      case "05":
          $timezone = "Asia/Vladivostok";
          break;
      case "06":
          $timezone = "Europe/Moscow";
          break;
      case "07":
          $timezone = "Europe/Volgograd";
          break;
      case "08":
          $timezone = "Europe/Samara";
          break;
      case "09":
          $timezone = "Europe/Moscow";
          break;
      case "10":
          $timezone = "Europe/Moscow";
          break;
      case "11":
          $timezone = "Asia/Irkutsk";
          break;
      case "13":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "14":
          $timezone = "Asia/Irkutsk";
          break;
      case "15":
          $timezone = "Asia/Anadyr";
          break;
      case "16":
          $timezone = "Europe/Samara";
          break;
      case "17":
          $timezone = "Europe/Volgograd";
          break;
      case "18":
          $timezone = "Asia/Krasnoyarsk";
          break;
      case "20":
          $timezone = "Asia/Irkutsk";
          break;
      case "21":
          $timezone = "Europe/Moscow";
          break;
      case "22":
          $timezone = "Europe/Volgograd";
          break;
      case "23":
          $timezone = "Europe/Kaliningrad";
          break;
      case "24":
          $timezone = "Europe/Volgograd";
          break;
      case "25":
          $timezone = "Europe/Moscow";
          break;
      case "26":
          $timezone = "Asia/Kamchatka";
          break;
      case "27":
          $timezone = "Europe/Volgograd";
          break;
      case "28":
          $timezone = "Europe/Moscow";
          break;
      case "29":
          $timezone = "Asia/Novokuznetsk";
          break;
      case "30":
          $timezone = "Asia/Vladivostok";
          break;
      case "31":
          $timezone = "Asia/Krasnoyarsk";
          break;
      case "32":
          $timezone = "Asia/Omsk";
          break;
      case "33":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "34":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "35":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "36":
          $timezone = "Asia/Anadyr";
          break;
      case "37":
          $timezone = "Europe/Moscow";
          break;
      case "38":
          $timezone = "Europe/Volgograd";
          break;
      case "39":
          $timezone = "Asia/Krasnoyarsk";
          break;
      case "40":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "41":
          $timezone = "Europe/Moscow";
          break;
      case "42":
          $timezone = "Europe/Moscow";
          break;
      case "43":
          $timezone = "Europe/Moscow";
          break;
      case "44":
          $timezone = "Asia/Magadan";
          break;
      case "45":
          $timezone = "Europe/Samara";
          break;
      case "46":
          $timezone = "Europe/Samara";
          break;
      case "47":
          $timezone = "Europe/Moscow";
          break;
      case "48":
          $timezone = "Europe/Moscow";
          break;
      case "49":
          $timezone = "Europe/Moscow";
          break;
      case "50":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "51":
          $timezone = "Europe/Moscow";
          break;
      case "52":
          $timezone = "Europe/Moscow";
          break;
      case "53":
          $timezone = "Asia/Novosibirsk";
          break;
      case "54":
          $timezone = "Asia/Omsk";
          break;
      case "55":
          $timezone = "Europe/Samara";
          break;
      case "56":
          $timezone = "Europe/Moscow";
          break;
      case "57":
          $timezone = "Europe/Samara";
          break;
      case "58":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "59":
          $timezone = "Asia/Vladivostok";
          break;
      case "60":
          $timezone = "Europe/Kaliningrad";
          break;
      case "61":
          $timezone = "Europe/Volgograd";
          break;
      case "62":
          $timezone = "Europe/Moscow";
          break;
      case "63":
          $timezone = "Asia/Yakutsk";
          break;
      case "64":
          $timezone = "Asia/Sakhalin";
          break;
      case "65":
          $timezone = "Europe/Samara";
          break;
      case "66":
          $timezone = "Europe/Moscow";
          break;
      case "67":
          $timezone = "Europe/Samara";
          break;
      case "68":
          $timezone = "Europe/Volgograd";
          break;
      case "69":
          $timezone = "Europe/Moscow";
          break;
      case "70":
          $timezone = "Europe/Volgograd";
          break;
      case "71":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "72":
          $timezone = "Europe/Moscow";
          break;
      case "73":
          $timezone = "Europe/Samara";
          break;
      case "74":
          $timezone = "Asia/Krasnoyarsk";
          break;
      case "75":
          $timezone = "Asia/Novosibirsk";
          break;
      case "76":
          $timezone = "Europe/Moscow";
          break;
      case "77":
          $timezone = "Europe/Moscow";
          break;
      case "78":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "79":
          $timezone = "Asia/Irkutsk";
          break;
      case "80":
          $timezone = "Asia/Yekaterinburg";
          break;
      case "81":
          $timezone = "Europe/Samara";
          break;
      case "82":
          $timezone = "Asia/Irkutsk";
          break;
      case "83":
          $timezone = "Europe/Moscow";
          break;
      case "84":
          $timezone = "Europe/Volgograd";
          break;
      case "85":
          $timezone = "Europe/Moscow";
          break;
      case "86":
          $timezone = "Europe/Moscow";
          break;
      case "87":
          $timezone = "Asia/Novosibirsk";
          break;
      case "88":
          $timezone = "Europe/Moscow";
          break;
      case "89":
          $timezone = "Asia/Vladivostok";
          break;
      }
      break;
    case "UA":
        switch ($region) {
      case "01":
          $timezone = "Europe/Kiev";
          break;
      case "02":
          $timezone = "Europe/Kiev";
          break;
      case "03":
          $timezone = "Europe/Uzhgorod";
          break;
      case "04":
          $timezone = "Europe/Zaporozhye";
          break;
      case "05":
          $timezone = "Europe/Zaporozhye";
          break;
      case "06":
          $timezone = "Europe/Uzhgorod";
          break;
      case "07":
          $timezone = "Europe/Zaporozhye";
          break;
      case "08":
          $timezone = "Europe/Simferopol";
          break;
      case "09":
          $timezone = "Europe/Kiev";
          break;
      case "10":
          $timezone = "Europe/Zaporozhye";
          break;
      case "11":
          $timezone = "Europe/Simferopol";
          break;
      case "13":
          $timezone = "Europe/Kiev";
          break;
      case "14":
          $timezone = "Europe/Zaporozhye";
          break;
      case "15":
          $timezone = "Europe/Uzhgorod";
          break;
      case "16":
          $timezone = "Europe/Zaporozhye";
          break;
      case "17":
          $timezone = "Europe/Simferopol";
          break;
      case "18":
          $timezone = "Europe/Zaporozhye";
          break;
      case "19":
          $timezone = "Europe/Kiev";
          break;
      case "20":
          $timezone = "Europe/Simferopol";
          break;
      case "21":
          $timezone = "Europe/Kiev";
          break;
      case "22":
          $timezone = "Europe/Uzhgorod";
          break;
      case "23":
          $timezone = "Europe/Kiev";
          break;
      case "24":
          $timezone = "Europe/Uzhgorod";
          break;
      case "25":
          $timezone = "Europe/Uzhgorod";
          break;
      case "26":
          $timezone = "Europe/Zaporozhye";
          break;
      case "27":
          $timezone = "Europe/Kiev";
          break;
      }
      break;
    case "UZ":
        switch ($region) {
      case "01":
          $timezone = "Asia/Tashkent";
          break;
      case "02":
          $timezone = "Asia/Samarkand";
          break;
      case "03":
          $timezone = "Asia/Tashkent";
          break;
      case "06":
          $timezone = "Asia/Tashkent";
          break;
      case "07":
          $timezone = "Asia/Samarkand";
          break;
      case "08":
          $timezone = "Asia/Samarkand";
          break;
      case "09":
          $timezone = "Asia/Samarkand";
          break;
      case "10":
          $timezone = "Asia/Samarkand";
          break;
      case "12":
          $timezone = "Asia/Samarkand";
          break;
      case "13":
          $timezone = "Asia/Tashkent";
          break;
      case "14":
          $timezone = "Asia/Tashkent";
          break;
      }
      break;
    case "TL":
        $timezone = "Asia/Dili";
        break;
    case "PF":
        $timezone = "Pacific/Marquesas";
        break;
      }
      return $timezone;
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

function getLocalTime($timezone, $timestamp) {
    $tz = getenv('TZ');
    putenv("TZ=$timezone");
    if (!$timestamp) {
        return;
    }
    $LocalTime = date("Y-m-d H:i:s", $timestamp);
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

function getSipAccountFromX509Certificate($account='') {

     if (!$account) {
        print _('Error, please specify an account');
        return false;
     }

     list($username, $domain) = explode("@",$account);

     if (!$username || !$domain) {
         print _("Invalid account provided");
         return false;
     }

     if (!$_SERVER[SSL_CLIENT_CERT]) {
     	print _("Error: No X.509 client certificate provided\n");
        return false;
     }

     if (!$cert=openssl_x509_parse($_SERVER[SSL_CLIENT_CERT])) {
     	print _("Error: Failed to parse X.509 client certificate\n");
        return false;
     }

     require("/etc/cdrtool/ngnpro_engines.inc");

     global $domainFilters, $resellerFilters, $soapEngines;

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

     // Filter
     $filter=array('username' => $username,
                   'domain'   => $domain,
                   'owner'    => intval($cert['subject']['CN'])
                   );

     // Range
     $range=array('start' => 0,
                  'count' => 10
                  );

     $orderBy = array('attribute' => 'changeDate',
                      'direction' => 'DESC'
                      );

     // Compose query
     $Query=array('filter'  => $filter,
                  'orderBy' => $orderBy,
                  'range'   => $range
                  );

     // Call function
     $result     = $SipPort->getAccounts($Query);

     if ((new PEAR)->isError($result)) {
         $error_msg  = $result->getMessage();
         $error_fault= $result->getFault();
         $error_code = $result->getCode();
         printf ("<p><font color=red>Error from %s (SipPort): %s (%s): %s</font>",$soapEngines[$credentials['engine']]['url'],$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
         return false;
     }

     if ($result->total != 1) {
         return false;
     }

     $credentials['account']  = $account;
     $credentials['customer'] = $result->customer;
     $credentials['reseller'] = $result->reseller;

     return $credentials;
}

function getSipAccountFromHTTPDigest () {

    require("/etc/cdrtool/enrollment/config.ini");

    if (!is_array($enrollment) || !strlen($enrollment['nonce_key'])) {
        $log= 'Error: Missing nonce in enrollment settings';
        syslog(LOG_NOTICE, $log);
        die($log);
        return false;
    }

    if ($_REQUEST['realm']) {
        // required by Blink cocoa
        $realm=$_REQUEST['realm'];
        $a=explode("@",$realm);
        if (count($a) == 2) {
            $realm = $a[1];
        }
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

        //syslog(LOG_NOTICE, sprintf ("SIP settings page: sent auth request for realm %s to %s", $realm, $_SERVER['REMOTE_ADDR']));
        die();
    }

    // analyze the PHP_AUTH_DIGEST variable
    if (!($data = http_digest_parse($_SERVER['PHP_AUTH_DIGEST'])) ||
        !isset($data['username'])) {
        $log=sprintf("SIP settings page: Invalid credentials from %s", $_SERVER['REMOTE_ADDR']);
        syslog(LOG_NOTICE, $log);
        die($log);
    }

    // generate the valid response
    $username    = $data['username'];

    if (strstr($username, '@')) {
       $a = explode("@",$username);
       $username = $a[0];
       $domain   = $a[1];
    } else {
       $domain = $realm;
    }

    require("/etc/cdrtool/ngnpro_engines.inc");

    global $domainFilters, $resellerFilters, $soapEngines ;

    $credentials['account']    = sprintf("%s@%s",$username, $domain);

    if ($domainFilters[$domain]['sip_engine']) {
        $credentials['engine']   = $domainFilters[$domain]['sip_engine'];
        $credentials['customer'] = $domainFilters[$domain]['customer'];
        $credentials['reseller'] = $domainFilters[$domain]['reseller'];

    } else if ($domainFilters['default']['sip_engine']) {
        $credentials['engine']=$domainFilters['default']['sip_engine'];
    } else {
        $log=sprintf("SIP settings page error: no domainFilter available in ngnpro_engines.inc from %s", $_SERVER['REMOTE_ADDR']);
        syslog(LOG_NOTICE, $log);
        die();
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

    $result = $SipPort->getAccount(array("username" =>$username,"domain"   =>$domain));

    if ((new PEAR)->isError($result)) {
        $error_msg  = $result->getMessage();
        $error_fault= $result->getFault();
        $error_code = $result->getCode();
    	header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');
        $log=sprintf("SIP settings page error: non-existent username %s from %s", $credentials['account'], $_SERVER['REMOTE_ADDR']);
        syslog(LOG_NOTICE, $log);
        die();
    }

    $web_password='';
    foreach ($result->properties as $_property) {
        if ($_property->name == 'web_password') {
            //$web_password = explode(":", $_property->value, -1);
            $split=explode(":",$_property->value);
            $web_password=$split['0'];
            break;
        }
    }

    if (!empty($web_password)) {
        //$A1 = md5($data['username'] . ':' . $realm . ':' . $data['password']);
        $A1 = $web_password;
        $login_type_log = 'web password';

        //$log=sprintf("TEST %s %s %s %s", $data['username'], $realm, $web_password , $data['nonce']);
        //syslog(LOG_NOTICE, $log);
//    } else if (strstr($data['username'], '@')) {
//        $A1 = md5($data['username'] . ':' . $realm . ':' . $result->password);
//       $login_type_log = 'cleartext legacy password';
    } else if ($result->ha1) {
        $login_type_log = sprintf('encrypted password');
        $A1 = $result->ha1;
    } else {
        $A1 = md5($data['username'] . ':' . $realm . ':' . $result->password);
        $login_type_log = 'cleartext password';
    }

    $A2 = md5($_SERVER['REQUEST_METHOD'].':'.$data['uri']);
    $valid_response = md5($A1.':'.$data['nonce'].':'.$data['nc'].':'.$data['cnonce'].':'.$data['qop'].':'.$A2);

    if ($data['response'] != $valid_response ) {
    	header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');

        $log=sprintf("SIP settings page error: wrong credentials using %s for %s from %s", $login_type_log, $credentials['account'], $_SERVER['REMOTE_ADDR']);
        syslog(LOG_NOTICE, $log);
        die();
    }
    // check nonce

	$client_nonce_els=explode(":",base64_decode($data['nonce']));

	if (md5($client_nonce_els[0].":".$_key) != $client_nonce_els[1]) {
    	header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",opaque="'.md5($realm).'"');

        $log=sprintf("SIP settings page error: wrong nonce for %s from %s", $credentials['account'], $_SERVER['REMOTE_ADDR']);
        syslog(LOG_NOTICE, $log);
        die();
    }


	if (microtime(true) > $client_nonce_els[0]) {
        // nonce is stale
        header('HTTP/1.1 401 Unauthorized');
        header('WWW-Authenticate: Digest realm="'.$realm.
               '",qop="auth",nonce="'.$nonce.'",stale=true,opaque="'.md5($realm).'"');

        $log=sprintf("SIP settings page error: nonce has expired for %s from %s", $username, $_SERVER['REMOTE_ADDR']);
        syslog(LOG_NOTICE, $log);
        die();
    }

    $log=sprintf("SIP settings page: %s logged in using %s from %s", $credentials['account'], $login_type_log, $_SERVER['REMOTE_ADDR']);
    syslog(LOG_NOTICE, $log);

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

    if ($_REQUEST['action']) {
        $log_action=$_REQUEST['action'];
    } else {
        $log_action='load main page';
    }
    $log=sprintf("SIP settings page: %s for %s from %s", $log_action, $account, $_SERVER['REMOTE_ADDR']);
    syslog(LOG_NOTICE, $log);

    if (!strstr($_REQUEST['action'],'get_') &&
        !strstr($_REQUEST['action'],'set_') &&
        !strstr($_REQUEST['action'],'put_') &&
        !strstr($_REQUEST['action'],'delete_') &&
        !strstr($_REQUEST['action'],'export_') &&
        !strstr($_REQUEST['action'],'add_')) {
        $title  = "$account";

	if (array_key_exists($SipSettings->tab, $SipSettings->tabs)) {
	    $title = $SipSettings->tabs[$SipSettings->tab]. " - ". $title;
	}

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
    } else if ($_REQUEST['action']=="set reject") {
        $SipSettings->setRejectMembers();
    } else if ($_REQUEST['action']=="set accept rules") {
        $SipSettings->setAcceptRules();
    } else if ($_REQUEST['action']=="set aliases") {
        $SipSettings->setAliases();
    } else if ($_REQUEST['action']=="send email") {
        $SipSettings->sendEmail();
    } else if ($_REQUEST['action']=="delete account") {
        $SipSettings->deleteAccount();
    } else if ($_REQUEST['action']=="delete_account") {
       // print "<pre>";
       // print_r($SipSettings->Preferences);
        $date1= new datetime($SipSettings->Preferences['account_delete_request']);
        $today= new datetime('now');
        if ($date1->diff($today)->d <= '2' && $SipSettings->Preferences['account_delete_request'] ) {

            $SipSettings->SipPort->addHeader($SipSettings->SoapAuth);
            $result = $SipSettings->SipPort->deleteAccount($SipSettings->sipId);

            if ((new PEAR)->isError($result)) {
                $error_msg  = $result->getMessage();
                $error_fault= $result->getFault();
                $error_code = $result->getCode();
                $_msg=sprintf ("Error (SipPort): %s (%s): %s",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                $return=array('success'       => false,
                            'error_message' => $_msg
                            );
                return false;
            }  else {
                printf("<p>The account %s has been removed</p>",$SipSettings->account);
                $SipSettings->sendRemoveAccount();
                //print "<script>var t1=setTimeout(window.location.href = 'sip_logout.phtml',5000);</script>"
                print "<a href=sip_logout.phtml>";
                print _("Click here to Logout");
                print "</a>";
                //$auth->logout();
                //$sess->delete();
                return true;
            }
        } else {
            printf("The delete request for account %s has expired or is not valid",$SipSettings->account);
            return false;
        }
        return true ;
        //$SipSettings->deleteAccount();
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
    } else if ($_REQUEST['action'] == 'get_journal_entries'){
        $SipSettings->getJournalEntries();
        print json_encode($SipSettings->journalEntries);
        return true;
    } else if ($_REQUEST['action'] == 'put_journal_entries'){
        print json_encode($SipSettings->putJournalEntries());
        return true;
    } else if ($_REQUEST['action'] == 'delete_journal_entries'){
        print json_encode($SipSettings->deleteJournalEntries());
        return true;
    } else if ($_REQUEST['action'] == 'get_reject_rules'){
        $SipSettings->getRejectMembers();
        print json_encode($SipSettings->rejectMembers);
        return true;
    } else if ($_REQUEST['action'] == 'get_history'){
        $SipSettings->getHistory('completed');
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

        if ((new PEAR)->isError($result)) {
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

            if ((new PEAR)->isError($result)) {
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
    var $init                       = false;
    var $create_voicemail           = false;
    var $send_email_notification    = true;
    var $create_email_alias         = false;
    var $create_customer            = true;
    var $timezones                  = array();
    var $default_timezone           = 'Europe/Amsterdam';
    var $configuration_file         = '/etc/cdrtool/enrollment/config.ini';
    var $allow_pstn                 = 1;
    var $quota                      = 50;
    var $prepaid                    = 1;
    var $create_certificate         = 0;
    var $customer_belongs_to_reseller = false;

    function log_action($action){
        global $auth;
        $location = "Unknown";
        $_loc=geoip_record_by_name($_SERVER['REMOTE_ADDR']);
        if ($_loc['country_name']) {
            $location = $_loc['country_name'];
            }
        $log = sprintf("CDRTool login username=%s, IP=%s, location=%s, action=%s, script=%s",
        $auth->auth["uname"],
        $_SERVER['REMOTE_ADDR'],
        $location,
        $action,
        $_SERVER['PHP_SELF']
        );
        syslog(LOG_NOTICE, $log);
    }

    function Enrollment()
    {
        require($this->configuration_file);
        require("/etc/cdrtool/ngnpro_engines.inc");

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

        $this->reseller          = $this->enrollment['reseller'];
        $this->outbound_proxy    = $this->enrollment['outbound_proxy'];
        $this->xcap_root         = $this->enrollment['xcap_root'];
        $this->msrp_relay        = $this->enrollment['msrp_relay'];
        $this->settings_url      = $this->enrollment['settings_url'];
        $this->ldap_hostname     = $this->enrollment['ldap_hostname'];
        $this->ldap_dn           = $this->enrollment['ldap_dn'];
        $this->conference_server = $this->enrollment['conference_server'];

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
            $this->customerRecords    = new $_customer_class($this->CustomerSoapEngine);
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

            $this->log_action("Create owner account ($firstname $lastname) ");

            $timezone=$_REQUEST['tzinfo'];

            if (!in_array($timezone, $this->timezones)) {
            	$timezone=$this->default_timezone;
            }

            $location = lookupGeoLocation($_SERVER['REMOTE_ADDR']);

            $customer=array(
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

            if ($location['country_code'] == 'NL') {
                $customer['tel'] = '+31999999999';
            } else if ($location['country_code'] == 'US') {
                $customer['tel'] = sprintf ("+1%s9999999",$location['area_code']);
            } else {
                $customer['tel'] = '+19999999999';
            }

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
        $this->sipRecords    = new $_sip_class($this->SipSoapEngine);
        $this->sipRecords->html=false;


        $sip_properties[]=array('name'=> 'ip',                 'value' => $_SERVER['REMOTE_ADDR']);
        $sip_properties[]=array('name'=> 'registration_email', 'value' => $_REQUEST['email']);

        $languages=array("en","ro","nl","es","de");

        if (isset($_REQUEST['lang'])){
            if (in_array($_REQUEST['lang'],$languages)) {
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
                            'timezone'  => $timezone,
                            'prepaid'   => $this->prepaid,
                            'pstn'      => $this->allow_pstn,
                            'quota'     => $this->quota,
                            'owner'     => intval($owner),
                            'groups'    => $this->groups,
                            'properties'=> $sip_properties
                            );

        $this->log_action("Create SIP account ($sip_addres)");

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
                if (!$passport = $this->generateCertificate($sip_address,$_REQUEST['email'],$_REQUEST['password'])) {
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
                if ($SipSettings = new $this->sipClass($sip_address,$this->sipLoginCredentials,$this->soapEngines)) {

                    if ($this->create_voicemail) {
                        // Add voicemail account
                        $this->log_action("Add voicemail account ($sip_address)");
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
                $this->log_action("Add email alias ($sip_address)");
                $emailEngine           = 'email_aliases@'.$this->emailEngine;
                $this->EmailSoapEngine = new SoapEngine($emailEngine,$this->soapEngines,$this->sipLoginCredentials);
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

    function generateCertificate($sip_address,$email,$password) {
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
    var $deny_countries      = array();
	var $allow_countries     = array();
	var $deny_ips            = array();
    var $make_credit_checks  = true;
    var $transaction_results = array('success' => false);
    var $vat                 = 0;

    function PaypalProcessor($account) {
        require('cc_processor.php');
        $this->CardProcessor = new CreditCardProcessor();
        $this->account = &$account;
	}

    function refundTransaction($transaction_id) {
    }

    function doDirectPayment($basket) {

        if (!is_object($this->account)) {
            print "
            <tr>
            <td colspan=3>
            ";

            print 'Invalid account data';

            print "
            </td>
            </tr>
            ";

            return false;
        }

        if (!is_array($basket)) {
            print "
            <tr>
            <td colspan=3>
            ";

            print 'Invalid basket data';

            print "
            </td>
            </tr>
            ";
            return false;
        }

        if (is_array($this->test_credit_cards) && in_array($_POST['creditCardNumber'], $this->test_credit_cards)) {
	        $this->CardProcessor->environment='sandbox';
        }

        $this->CardProcessor->chapter_class  = 'chapter';
        $this->CardProcessor->odd_row_class  = 'oddc';
        $this->CardProcessor->even_row_class = 'evenc';

        $this->CardProcessor->note = $this->account->account;
        $this->CardProcessor->account = $this->account->account;

        $this->CardProcessor->vat = $this->vat;

        // set hidden elements we need to preserve in the shopping cart application
        $this->CardProcessor->hidden_elements = $this->account->hiddenElements;

        // load shopping items
        $this->CardProcessor->cart_items=$basket;

        // load user information from owner information if available otherwise from sip account settings

        if ($this->account->owner_information['firstName']) {
	    	$this->CardProcessor->user_account['FirstName']=$this->account->owner_information['firstName'];
    	} else {
        	$this->CardProcessor->user_account['FirstName']=$this->account->firstName;
        }

        if ($this->account->owner_information['lastName']) {
        	$this->CardProcessor->user_account['LastName']=$this->account->owner_information['lastName'];
        } else {
        	$this->CardProcessor->user_account['LastName']=$this->account->lastName;
        }

        if ($this->account->owner_information['email']) {
        	$this->CardProcessor->user_account['Email']=$this->account->owner_information['email'];
        } else {
        	$this->CardProcessor->user_account['Email']=$this->account->email;
        }

        if ($this->account->owner_information['address'] && $this->account->owner_information['address']!= 'Unknown') {
        	$this->CardProcessor->user_account['Address1']=$this->account->owner_information['address'];
        } else {
        	$this->CardProcessor->user_account['Address1']='';
        }

        if ($this->account->owner_information['city'] && $this->account->owner_information['city']!= 'Unknown') {
        	$this->CardProcessor->user_account['City']=$this->account->owner_information['city'];
        } else {
        	$this->CardProcessor->user_account['City']='';
        }

        if ($this->account->owner_information['country'] && $this->account->owner_information['country']!= 'Unknown') {
        	$this->CardProcessor->user_account['Country']=$this->account->owner_information['country'];
        } else {
	        $this->CardProcessor->user_account['Country']='';
        }

        if ($this->account->owner_information['state'] && $this->account->owner_information['state']!= 'Unknown') {
    	    $this->CardProcessor->user_account['State']=$this->account->owner_information['state'];
        } else {
        	$this->CardProcessor->user_account['State']='';
        }

        if ($this->account->owner_information['postcode'] && $this->account->owner_information['postcode']!= 'Unknown') {
	        $this->CardProcessor->user_account['PostCode']=$this->account->owner_information['postcode'];
        } else {
    	    $this->CardProcessor->user_account['PostCode']='';
        }

        if ($_REQUEST['purchase'] == '1' ) {
            $chapter=sprintf(_("Transaction Results"));
            $this->account->showChapter($chapter);

            print "
            <tr>
            <td colspan=3>
            ";

            // ensure that submit requests are coming only from the current page
            if ($_SERVER['HTTP_REFERER'] == $this->CardProcessor->getPageURL()) {

                // check submitted values
                $errors = $this->CardProcessor->checkForm($_POST);
                if (count($errors) > 0){
                    print $this->CardProcessor->displayFormErrors($errors);

                    foreach (array_keys($errors) as $key) {
                        $log_text.=sprintf("%s:%s ",$errors[$key]['field'],$errors[$key]['desc']);
                    }

                    $log=sprintf("CC transaction for %s failed with error: %s",$this->account->account,$log_text);
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

                // process the payment
                $b=time();

                $pay_process_results = $this->CardProcessor->processPayment($_POST);
                if(count($pay_process_results['error']) > 0){
                    // there was a problem with payment
                    // show error and stop

                    if ($pay_process_results['error']['field'] == 'reload') {
                        print $pay_process_results['error']['desc'];
                    } else {
                        print $this->CardProcessor->displayProcessErrors($pay_process_results['error']);
                    }

                    $e=time();
                    $d=$e-$b;

                    $log=sprintf("CC transaction for %s failed with error: %s (%s) after %d seconds",
                    $this->account->account,
                    $pay_process_results['error']['short_message'],
                    $pay_process_results['error']['error_code'],
                    $d
                    );

                    syslog(LOG_NOTICE, $log);

                    return false;
                } else {

                    $e=time();
                    $d=$e-$b;

                    $log=sprintf("CC transaction %s for %s completed succesfully in %d seconds",
                    $pay_process_results['success']['desc']->TransactionID,
                    $this->account->account,
                    $d
                    );
                    syslog(LOG_NOTICE, $log);

                    print "<p>";
                    print _("Transaction completed sucessfully. ");

                    /*
                    if ($this->CardProcessor->environment!='sandbox' && $this->account->first_transaction) {
                        print "<p>";
                        print _("This is your first payment. ");

                        print "<p>";
                        print _("Please allow the time to check the validity of your transaction before activating your Credit. ");

                        print "<p>";
                        print _("You can speed up the validation process by sending a copy of an utility bill (electriciy, gas or TV) that displays your address. ");

                        print "<p>";
                        printf (_("For questions related to your payments or to request a refund please email to <i>%s</i> and mention your transaction id <b>%s</b>. "),
                        $this->account->billing_email,
                        $pay_process_results['success']['desc']->TransactionID
                        );

                        $this->make_credit_checks=true;

                    } else {
                       print "<p>";
                       print _("You may check your new balance in the Credit tab. ");
                    }
                    */
                }

                if ($this->account->Preferences['ip'] && $_loc=geoip_record_by_name($this->account->Preferences['ip'])) {
                    $enrollment_location=$_loc['country_name'].'/'.$_loc['city'];
                } else if ($this->account->Preferences['ip'] && $_loc=geoip_country_name_by_name($this->account->Preferences['ip'])) {
                    $enrollment_location=$_loc;
                } else {
                    $enrollment_location='Unknown';
                }

                if ($_loc=geoip_record_by_name($_SERVER['REMOTE_ADDR'])) {
                    $transaction_location=$_loc['country_name'].'/'.$_loc['city'];
                } else if ($_loc=geoip_country_name_by_name($_SERVER['REMOTE_ADDR'])) {
                    $transaction_location=$_loc;
                } else {
                    $transaction_location='Unknown';
                }

                if ($this->account->Preferences['timezone']) {
                    $timezone=$this->account->Preferences['timezone'];
                } else {
                    $timezone='Unknown';
                }

                $extra_information=array(
                                         'Account Page'         => $this->account->admin_url_absolute,
                                         'Account First Name'   => $this->account->firstName,
                                         'Account Last Name '   => $this->account->lastName,
                                         'Account Timezone'     => $this->account->timezone,
                                         'Enrollment IP'        => $this->account->Preferences['ip'],
                                         'Enrollment Location'  => $enrollment_location,
                                         'Enrollment Email'     => $this->account->Preferences['registration_email'],
                                         'Enrollment Timezone'  => $timezone,
                                         'Transaction Location' => $transaction_location
                                         );

                $result = $this->account->addInvoice($this->CardProcessor);
                if ($result) {
                    $extra_information['Invoice Page']=sprintf("https://admin.ag-projects.com/admin/invoice.phtml?iId=%d&adminonly=1",$result['invoice']);
                }

                if ($this->CardProcessor->saveOrder($_POST,$pay_process_results,$extra_information)) {

                    $this->transaction_results=array('success' => true,
                                                     'id'      => $this->CardProcessor->transaction_data['TRANSACTION_ID']
                                                     );

                    return true;

                } else {
                    $log=sprintf("Error: SIP Account %s - CC transaction %s failed to save order",$this->account->account, $this->CardProcessor->transaction_data['TRANSACTION_ID']);
                    syslog(LOG_NOTICE, $log);
                    return false;
                }

            } else {
                print _("Invalid CC Request");
                return false;
            }

            print "
            </td>
            </tr>
            ";

        } else {

            print "
            <tr>
            <td colspan=3>
            ";

            // print the submit form
            $arr_form_page_objects = $this->CardProcessor->showSubmitForm();
            print $arr_form_page_objects['page_body_content'];

            print "
            </td>
            </tr>
            ";

    	}

    }

    function fraudDetected() {

        if (count($this->deny_ips)) {
            foreach ($this->deny_ips as $_ip) {
                if ($this->account->Preferences['ip'] && preg_match("/^$_ip/",$this->account->Preferences['ip'])) {
                    $this->fraud_reason=$this->account->Preferences['ip'].' is Blocked';
                    return true;
                }

                if (preg_match("/^$_ip/",$_SERVER['REMOTE_ADDR'])) {
                    $this->fraud_reason=$_SERVER['REMOTE_ADDR'].' is a Blocked';
                    return true;
                }
            }
        }

        if (count($this->deny_countries)) {

            if ($_loc=geoip_record_by_name($this->account->Preferences['ip'])) {
                if (in_array($_loc['country_name'],$this->deny_countries)) {
                    $this->fraud_reason=$_loc['country_name'].' is Blocked';
                    return true;
                }
            }
        }

        if (count($this->allow_countries)) {
            if ($_loc=geoip_record_by_name($this->account->Preferences['ip'])) {
                if (!in_array($_loc['country_name'],$this->allow_countries)) {
                    $this->fraud_reason=$_loc['country_name'].' is Not Allowed';
                    return true;
                }
            }
        }


        if (count($this->deny_email_domains)) {
            if (count($this->accept_email_addresses)) {
                if (in_array($this->account->email,$this->accept_email_addresses)) return false;
            }

            list($user,$domain)= explode("@",$this->account->email);
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

class DIDProcessor {

    function DIDProcessor() {

        /*
        http://www.didww.com/support/
        API help page: http://open.didww.com
        */

        $this->db = new DB_CDRTool();

        require('didww_soap_library.php');
        include("/etc/cdrtool/enrollment/config.ini");

        if (!$enrollment['did_username'] || !$enrollment['did_key']) {
            print '<p>Error: Missing DID engine credentials';
            return false;
        }

		if ($enrollment['did_environment'] == 'production') {
        	$this->did_engine = new WebService_DID_World_Wide__DID_World_Wide_Port();
            $this->auth_string = sha1($enrollment['did_username'].$enrollment['did_key']);
            $this->environment='production';
        } else {
            print "<h2>Testing DID environment</h2>";
            flush();
        	$this->did_engine = new WebService_DID_World_Wide__DID_World_Wide_Port_Testing();
            $this->auth_string = sha1($enrollment['did_username'].$enrollment['did_key'].'sandbox');
            $this->environment='testing';
        }

        $this->did_engine->_options['timeout'] = 30;

    }

    function getPrefixesFromRemote () {

        if (!$this->auth_string) return false;

        $result = $this->did_engine->didww_getdidwwregions($this->auth_string,$country);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        }  else {
            foreach ($result as $_country) {
                foreach ($_country->cities as $_city) {
                	$prefix = $_country->country_prefix.$_city->city_prefix;
                    if (!$_city->isavailable) continue;
                    $prefixes[$prefix]=array('country_prefix' => trim($_country->country_prefix),
                    	                     'country_name'   => trim($_country->country_name),
                                             'country_iso'    => trim($_country->country_iso),
                                             'city_name'      => trim($_city->city_name),
                                             'city_prefix'    => trim($_city->city_prefix),
                                             'setup'          => $_city->setup,
                                             'monthly'        => $_city->monthly
                                              );
                }
            }
        }

        return $prefixes;
    }

    function getPrefixes () {

        $query=sprintf("select * from ddi_cache where environment = '%s' and DATE_ADD(date, INTERVAL +1 day) > NOW()",addslashes($this->environment));

        if (!$this->db->query($query)) return false;

        if ($this->db->num_rows()) {
        	$this->db->next_record();
            $prefixes = json_decode($this->db->f('cache'),true);
            if (!is_array($prefixes)) {
            	$prefixes = $this->cachePrefixes();
            }
        } else {
            $prefixes=$this->cachePrefixes();
        }

        return $prefixes;
    }

    function cachePrefixes() {
        if ($prefixes = $this->getPrefixesFromRemote()) {

            $query=sprintf("delete from ddi_cache where environment = '%s'",addslashes($this->environment));
            $this->db->query($query);

            $query=sprintf("insert into ddi_cache (cache,date,environment) values ('%s', NOW(),'%s')",addslashes(json_encode($prefixes)),addslashes($this->environment));
            $this->db->query($query);
            return $prefixes;
        } else {
            return false;
        }

    }

    function getResellerInfo() {

        if (!$this->auth_string) return false;

    	$result = $this->did_engine->didww_getdidwwapidetails($this->auth_string);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            print "<pre>";
            print_r($result);
            print "</pre>";
        }
    }

    function createOrder($data) {
        if (!$this->auth_string) return false;

        print "<pre>";
        print_r($data);
        print "</pre>";

        $result = $this->did_engine->didww_ordercreate($this->auth_string,
                                                       $data['customer_id'],
                                                       $data['country_iso'],
                                                       $data['city_prefix'],
                                                       $data['period'],
                                                       $data['map_data'],
                                                       $data['prepaid_funds'],
                                                       $data['uniq_hash']
                                                       );

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $query=sprintf ("insert into ddi_numbers (
                                         `customer_id`,
                                         `country_name`,
                                         `city_name`,
                                         `did_number`,
                                         `did_status`,
                                         `did_timeleft`,
                                         `did_expire_date_gmt`,
                                         `order_id`,
                                         `order_status`,
                                         `sip_address`,
                                         `did_setup`,
                                         `did_monthly`,
                                         `did_period`,
                                         `prepaid_balance`,
                                         `environment`
                                         )
                                       values
                                        (
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s',
                                         '%s'
                                         )
                                        ",
                                        addslashes($data['customer_id']),
                                        addslashes($result->country_name),
                                        addslashes($result->city_name),
                                        addslashes($result->did_number),
                                        addslashes($result->did_status),
                                        addslashes($result->did_timeleft),
                                        addslashes($result->did_expire_date_gmt),
                                        addslashes($result->order_id),
                                        addslashes($result->order_status),
                                        addslashes($data['map_data']['map_detail']),
                                        addslashes($result->did_setup),
                                        addslashes($result->did_monthly),
                                        addslashes($result->did_period),
                                        addslashes($result->prepaid_balance),
                                        addslashes($this->environment)
                                        );

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for DID createOrder: %s (%s)",$this->db->Error,$this->db->Errno);
                print $log;
        		syslog(LOG_NOTICE, $log);
            }

        }

    }

    function renewOrder($data) {
        if (!$this->auth_string) return false;

        print "<pre>";
        print_r($data);
        print "</pre>";

        $result = $this->did_engine->didww_orderautorenew($this->auth_string,
                                                          $data['customer_id'],
                                                          $data['number'],
                                                          $data['period'],
                                                          $data['uniq_hash']
                                                          );

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $query=sprintf ("update ddi_numbers set did_timeleft = '%s' and did_expire_date_gmt = '%s' where did_number = '%s'
                                        ",
                                        addslashes($result->did_timeleft),
                                        addslashes($result->did_expire_date_gmt),
                                        addslashes($result->did_number)
                                        );

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for DID renewOrder: %s (%s)",$this->db->Error,$this->db->Errno);
                print $log;
        		syslog(LOG_NOTICE, $log);
            }

            print $query;
        }

    }

    function cancelOrder($data) {
        if (!$this->auth_string) return false;

        print "<pre>";
        print_r($data);
        print "</pre>";

        $result = $this->did_engine->didww_ordercancel($this->auth_string,
                                                          $data['customer_id'],
                                                          $data['number']
                                                          );

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();

            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf ("<p><font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            return false;
        } else {
            $query=sprintf ("delete from ddi_numbers where did_number = '%s'",addslashes($result->did_number));

            if (!$this->db->query($query)) {
                $log=sprintf ("Database error for DID cancelOrder: %s (%s)",$this->db->Error,$this->db->Errno);
                print $log;
        		syslog(LOG_NOTICE, $log);
            }

            print $query;
        }

    }

    function getOrders($sip_address) {
        $orders=array();

        $query=sprintf ("select * from ddi_numbers where sip_address = '%s' and environment = '%s'",addslashes($sip_address),addslashes($this->environment));

        if (!$this->db->query($query)) {
            $log=sprintf ("Database error for DID createOrder: %s (%s)",$this->db->Error,$this->db->Errno);
            print $log;
            syslog(LOG_NOTICE, $log);
        } else {
            while ($this->db->next_record()) {
                $orders[$this->db->f('did_number')]=array('country_name' => $this->db->f('country_name'),
                                                           'city_name' => $this->db->f('city_name'),
                                                           'did_status' => $this->db->f('did_status'),
                                                           'did_timeleft' => $this->db->f('did_timeleft'),
                                                           'did_expire_date_gmt' => $this->db->f('did_expire_date_gmt'),
                                                           'order_id' => $this->db->f('order_id'),
                                                           'order_status' => $this->db->f('order_status'),
                                                           'sip_address' => $this->db->f('sip_address'),
                                                           'did_setup' => $this->db->f('did_setup'),
                                                           'did_monthly' => $this->db->f('did_monthly')
                                                           );

        	}
        }

        return $orders;

    }
}

function RandomIdentifier($length = 30)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}

function getDisplayNameFromFromHeader($header)
{
    // match all words and whitespace, will be terminated by '<'
    preg_match('/[\w\s]+/', $header, $matches);
    $matches[0] = trim($matches[0]);
    return $matches[0];
}

if (file_exists("/etc/cdrtool/local/sip_settings.php")) {
    require_once '/etc/cdrtool/local/sip_settings.php';
}

?>
