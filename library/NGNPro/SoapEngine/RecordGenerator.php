<?php

class RecordGenerator extends SoapEngine
{
    //this class generates in bulk enum numbers and sip accounts

    var $template     = array();
    var $allowedPorts = array();
    var $maxRecords   = 500;
    var $minimum_number_length = 4;
    var $maximum_number_length = 15;
    var $default_ip_access_list = '';
    var $default_call_limit = '';

    public function __construct($generatorId, $record_generators, $soapEngines, $login_credentials = array())
    {
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
            $this->soapEngines = $this->getSoapEngineAllowed($soapEngines, $this->login_credentials['soap_filter']);
        } else {
            $this->soapEngines = $soapEngines;
        }

        if (in_array($this->record_generators[$generatorId]['sip_engine'],array_keys($this->soapEngines))) {
            // sip zones
            if (count($this->allowedPorts[$this->record_generators[$generatorId]['sip_engine']]) > 1 && !in_array('sip_accounts', $this->allowedPorts[$this->record_generators[$generatorId]['sip_engine']])) {
                // sip port not available
                dprint("sip port not avaliable");
            } else {
                $sip_engine           = 'sip_accounts@'.$this->record_generators[$generatorId]['sip_engine'];
                $this->SipSoapEngine = new SoapEngine($sip_engine, $soapEngines, $login_credentials);
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
            printf("<font color=red>Error: sip_engine %s does not exist</font>", $this->record_generators[$generatorId]['sip_engine']);
        }

        if (in_array($this->record_generators[$generatorId]['enum_engine'],array_keys($this->soapEngines))) {
            if (count($this->allowedPorts[$this->record_generators[$generatorId]['enum_engine']]) > 1 && !in_array('enum_numbers', $this->allowedPorts[$this->record_generators[$generatorId]['enum_engine']])) {
                dprint("enum port not avaliable");
                // enum port not available
            } else {
                // enum mappings
                $enum_engine          = 'enum_numbers@'.$this->record_generators[$generatorId]['enum_engine'];
                $this->EnumSoapEngine = new SoapEngine($enum_engine, $soapEngines, $login_credentials);
                $_enum_class          = $this->EnumSoapEngine->records_class;
                $this->enumRecords    = new $_enum_class($this->EnumSoapEngine);
            }

        } else {
            printf("<font color=red>Error: enum_engine %s does not exist</font>", $this->record_generators[$generatorId]['enum_engine']);
        }

        if (in_array($this->record_generators[$generatorId]['customer_engine'],array_keys($this->soapEngines))) {
            if (count($this->allowedPorts[$this->record_generators[$generatorId]['customer_engine']]) > 1 && !in_array('customers', $this->allowedPorts[$this->record_generators[$generatorId]['customer_engine']])) {
                dprint("customer port not avaliable");
            } else {
                $customer_engine          = 'customers@'.$this->record_generators[$generatorId]['customer_engine'];
                $this->CustomerSoapEngine = new SoapEngine($customer_engine, $soapEngines, $login_credentials);
                $_customer_class          = $this->CustomerSoapEngine->records_class;
                $this->customerRecords    = new $_customer_class($this->CustomerSoapEngine);
            }
        } else {
            printf("<font color=red>Error: customer_engine %s does not exist</font>", $this->record_generators[$generatorId]['customer_engine']);
        }

        if ($_REQUEST['reseller_filter']) $this->template['reseller']=intval($_REQUEST['reseller_filter']);
        if ($_REQUEST['customer_filter']) $this->template['customer']=intval($_REQUEST['customer_filter']);
    }

    function showGeneratorForm()
    {

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
                printf ("<option value='%s' %s>+%s under %s", $rangeId, $selected_range[$rangeId], $_range['prefix'], $_range['tld']);
            }
            print "</select>";
        }
        */

        list($_range['prefix'], $_range['tld'])=explode("@", $_REQUEST['range']);
        printf("<input type=hidden name=range value='%s'>+%s under %s", $_REQUEST['range'], $_range['prefix'], $_range['tld']);


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
        printf("
        <td align=right>
        <input type=text name=add_prefix size=10 maxsize=15 value='%s'>
        </td>
        <td>
        </td>
        </tr>
        ", $add_prefix);

        if ($_REQUEST['number_length']) {
            $number_length=$_REQUEST['number_length'];
        } else {
            $number_length = $this->sipRecords->getCustomerProperty('enum_generator_number_length');
        }

        print "
        <tr>
        <td>";
        print _("Number length:");
        printf(
            "
            <td align=right>
                <input type=text name=number_length size=10 maxsize=15 value='%s'>
            <tr>
            <td>
            ",
            $number_length
        );

        print _("SIP domain:");
        print "
        <td align=right>
        ";

        if (count($this->sipRecords->allowedDomains) > 0) {
            if ($_REQUEST['domain']) {
                $selected_domain[$_REQUEST['domain']]='selected';
            } elseif ($_last_domain=$this->sipRecords->getCustomerProperty('enum_generator_sip_domain')) {
                $selected_domain[$_last_domain] = 'selected';
            }

            print "
            <select name=domain>
            ";

            foreach ($this->sipRecords->allowedDomains as $domain) {
                printf("<option value='%s' %s>%s", $domain, $selected_domain[$domain], $domain);
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
        printf("
        <td align=right>
        <input type=text size=10 name=strip_digits value='%s'>
        </td>
        </tr>
        ", $strip_digits);
        print "
        <tr>
        <td>";
        print _("Owner:");
        printf("
        <td align=right><input type=text size=7 name=owner value='%s'>
        <td>", $_REQUEST['owner']);
        print "
        </td>
        </tr>";

        print "
        <tr>
        <td>";
        print _("Info:");
        printf("
        <td align=right><input type=text size=10 name=info value='%s'>
        <td>", $_REQUEST['info']);
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
            ", $checked_create_sip);

            if ($_REQUEST['pstn']) {
                $checked_pstn='checked';
            } else {
                $checked_pstn='';
            }

            print "
            <tr>
            <td>";
            print _("PSTN access");
            printf("
            <td align=right><input class=checkbox type=checkbox name=pstn value=1 %s>
            </td>
            </tr>
            ", $checked_pstn);

            if ($_REQUEST['prepaid']) {
                $checked_prepaid='checked';
            } else {
                $checked_prepaid='';
            }

            print "
            <tr>
            <td>";
            print _("Prepaid");
            printf("
            <td align=right><input type=checkbox name=prepaid value=1 %s>
            </td>
            </tr>
            ", $checked_prepaid);

            if ($_REQUEST['rpid_strip_digits']) {
                $rpid_strip_digits=$_REQUEST['rpid_strip_digits'];
            } elseif ($rpid_strip_digits = $this->sipRecords->getCustomerProperty('enum_generator_rpid_strip_digits')) {
            } else {
                $rpid_strip_digits=0;
            }

            print "
            <tr>
            <td>";
            print _("Strip digits from Caller-ID");
            printf("
            <td align=right><input type=text size=10 name=rpid_strip_digits value='%s'>
            </td>
            </tr>
            ", $rpid_strip_digits);

            print "
            <tr>
            <td>";
            print _("Quota");
            printf("
            <td align=right><input type=text size=10 name=quota value='%s'>
            </td>
            </tr>
            ", $_REQUEST['quota']);

            print "
            <tr>
            <td>";
            print _("Password");
            printf("
            <td align=right><input type=text size=10 name=password value='%s'>
            </td>
            </tr>
            ", $_REQUEST['password']);

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
            printf("
            <td align=right><input type=text size=10 name=call_limit value='%s'>
            </td>
            </tr>
            ", $call_limit);

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
            printf("
            <td align=right><input type=text size=40 name=ip_access_list value='%s'>
            </td>
            </tr>
            ", $ip_access_list);

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
        printf("<td align=right>
        Number of records:<input type=text size=10 name=nr_records value='%s'>
        ", $nr_records);
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

    function checkGenerateRequest()
    {
        // check number of records
        $this->template['create_sip']=trim($_REQUEST['create_sip']);

        $ip_access_list = preg_replace("/\s+/"," ", $_REQUEST['ip_access_list']);
        if (strlen($ip_access_list) and !check_ip_access_list(trim($ip_access_list), true)) {
            print "<font color=red>Error: IP access lists must be a space separated list of IP network/mask, example: 10.0.20.40/24</font>";
            return false;
        }

        $this->template['ip_access_list'] = trim($ip_access_list);

        if (strlen($_REQUEST['call_limit']) && !is_numeric($_REQUEST['call_limit'])) {
            print "<font color=red>Error: PSTN call limit must be numeric</font>";
            return false;
        }

        $this->template['call_limit']=$_REQUEST['call_limit'];

        $this->template['rpid_strip_digits']=intval($_REQUEST['rpid_strip_digits']);

        $this->template['info']=trim($_REQUEST['info']);

        $nr_records=trim($_REQUEST['nr_records']);

        if (!is_numeric($nr_records) || $nr_records < 1 || $nr_records > $this->maxRecords) {
            printf("<font color=red>Error: number of records must be a number between 1 and %d</font>", $this->maxRecords);
            return false;
        }

        $this->template['nr_records'] = $nr_records;

        $number_length=trim($_REQUEST['number_length']);

        if (!is_numeric($number_length) || $number_length < $this->minimum_number_length || $number_length > $this->maximum_number_length) {
            printf(
                "<font color=red>Error: number length must be a number between 4 and 15</font>",
                $this->minimum_number_length,
                $this->maximum_number_length
            );
            return false;
        }

        $this->template['number_length'] = $number_length;

        $strip_digits=trim($_REQUEST['strip_digits']);
        if (!is_numeric($strip_digits) || $strip_digits < 0 || $number_length < $strip_digits + 3) {
            printf("<font color=red>Error: strip digits + 3 must be smaller then %d</font>", $number_length);
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
        list($rangePrefix, $tld)=explode('@',trim($_REQUEST['range']));

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
            $this->template['firstNumber'] = $this->template['rangePrefix'].str_pad($start, $this->template['digitsAfterRange'],'0');
            $this->template['lastNumber']  = sprintf("%.0f", $this->template['firstNumber'] + pow(10, $this->template['digitsAfterRange']-strlen($this->template['add_prefix'])) - 1);
            $this->template['maxNumbers']  = pow(10, $this->template['digitsAfterRange']-strlen($this->template['add_prefix']));
        }

        dprint_r($this->template);

        if ($this->template['maxNumbers'] < $this->template['nr_records']) {
            printf(
                "<font color=red>Error: Insufficient numbers in range, requested = %d, available = %d</font>",
                $this->template['nr_records'],
                $this->template['maxNumbers']
            );
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
                printf(
                    "<font color=red>Error: cannot retrieve customer information for owner %d</font>",
                    $this->template['owner']
                );
            }
        }

        dprint_r($this->template);
        $i=0;

        while ($i < $this->template['nr_records']) {

            $number   = sprintf("%.0f", $this->template['firstNumber'] + $i);
            $username = substr($number, $this->template['strip_digits']);
            $mapto    = 'sip:'.$username.'@'.$this->template['domain'];

            print "<li>";
            printf ('Generating number +%s with mapping %s ', $number, $mapto);
            flush();

            $enumMapping = array('tld'      => $this->template['tld'],
                                 'number'   => $number,
                                 'type'     => 'sip',
                                 'mapto'    => $mapto,
                                 'info'     => $this->template['info'],
                                 'owner'    => $this->template['owner']
                                );

            if ($this->template['create_sip']) {
                if (preg_match("/^0/", $username)) {
                    printf ('SIP accounts starting with 0 are not generated (%s@%s)', $username, $this->template['domain']);
                    continue;
                }

                $groups=array();

                printf ('and sip account %s@%s ', $username, $this->template['domain']);

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
                    $strip_rpid = intval($this->template['rpid_strip_digits']);

                    if ($strip_rpid && strlen($number) > $strip_rpid) {
                        $sipAccount['rpid'] = substr($number, intval($this->template['rpid_strip_digits']));
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

    function printHiddenFormElements()
    {
        printf("<input type=hidden name=generatorId value='%s'>", $this->generatorId);

        if ($this->adminonly) {
            printf("<input type=hidden name=adminonly value='%s'>", $this->adminonly);
        }

        if ($this->template['customer']) {
            printf("<input type=hidden name=customer_filter value='%s'>", $this->template['customer']);
        }

        if ($this->template['reseller']) {
            printf("<input type=hidden name=reseller_filter value='%s'>", $this->template['reseller']);
        }

        foreach (array_keys($this->EnumSoapEngine->extraFormElements) as $element) {
            if (!strlen($this->EnumSoapEngine->extraFormElements[$element])) continue;
            printf("<input type=hidden name=%s value='%s'>\n", $element, $this->EnumSoapEngine->extraFormElements[$element]);
        }
    }

    function getSoapEngineAllowed($soapEngines, $filter)
    {
        // filter syntax:
        // $filter="engine1:port1,port2,port3 engine2 engine3";
        // where engine is a connection from ngnpro_engines.inc and
        // port is valid port from that engine like sip_accounts or enum_numbers

        $_filter_els = explode(" ", trim($filter));
        foreach (array_keys($soapEngines) as $_engine) {
            foreach ($_filter_els as $_filter) {
                unset($_allowed_engine);
                $_allowed_ports = array();

                list($_allowed_engine, $_allowed_ports_els) = explode(":", $_filter);

                if ($_allowed_ports_els) {
                    $_allowed_ports = explode(",", $_allowed_ports_els);
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

