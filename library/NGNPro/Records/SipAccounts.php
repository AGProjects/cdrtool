<?php

class SipAccounts extends Records
{
    public $selectionActiveExceptions = array('domain');

    var $sortElements = array(
        'changeDate' => 'Change date',
        'username'   => 'Username',
        'domain'     => 'Domain'
    );

    var $store_clear_text_passwords = true;
    var $default_account_type = 'postpaid';
    var $group_filter_list = array(
        'blocked'          => 'Blocked',
        'quota'            => 'Quota Exceeded',
        'prepaid'          => 'Prepaid',
        'free-pstn'        => 'PSTN Access',
        'anonymous'        => 'Anonymous',
        'anonymous-reject' => 'Reject Anonymous',
        'voicemail'        => 'Has Voicemail',
        'missed-calls'     => 'Missed Calls',
        'trunking'         => 'Trunking'
    );

    public function __construct($SoapEngine)
    {
        dprint("init SipAccounts");

        $this->filters = array(
            'username' => strtolower(trim($_REQUEST['username_filter'])),
            'domain'   => strtolower(trim($_REQUEST['domain_filter'])),
            'firstname'=> trim($_REQUEST['firstname_filter']),
            'lastname' => trim($_REQUEST['lastname_filter']),
            'email'    => htmlspecialchars(trim($_REQUEST['email_filter'])),
            'owner'    => trim($_REQUEST['owner_filter']),
            'customer' => trim($_REQUEST['customer_filter']),
            'reseller' => trim($_REQUEST['reseller_filter']),
            'group'    => trim($_REQUEST['group_filter'])
        );

        parent::__construct($SoapEngine);

        if (strlen($this->SoapEngine->call_limit)) {
            $this->platform_call_limit    = $this->SoapEngine->call_limit;
        } else {
            $this->platform_call_limit;
        }

        $this->getTimezones();
    }

    function getRecordKeys()
    {
        if (preg_match("/^(.*)@(.*)$/", $this->filters['username'], $m)) {
            $this->filters['username'] = $m[1];
            $this->filters['domain']   = $m[2];
        }

        // Filter
        $filter = array(
            'username' => $this->filters['username'],
            'domain'   => $this->filters['domain'],
            'firstName'=> $this->filters['firstname'],
            'lastName' => $this->filters['lastname'],
            'email'    => $this->filters['email'],
            'owner'    => intval($this->filters['owner']),
            'customer' => intval($this->filters['customer']),
            'reseller' => intval($this->filters['reseller']),
            'groups'   => array($this->filters['group'])
        );

        // Range
        $range = array(
            'start' => 0,
            'count' => 500
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
            'filter'  => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAccounts');

        // Call function
        $result     = $this->SoapEngine->soapclient->getAccounts($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            foreach ($result->accounts as $account) {
                $this->selectionKeys[] = array(
                    'username' => $account->id->username,
                    'domain'   => $account->id->domain
                );
            }

            return true;
        }

        return false;
    }

    function listRecords()
    {
        $this->getAllowedDomains();

        if (preg_match("/^(.*)@(.*)$/", $this->filters['username'], $m)) {
            $this->filters['username'] = $m[1];
            $this->filters['domain']   = $m[2];
        }

        $this->showSeachForm();

        // Filter
        $filter = array(
            'username' => $this->filters['username'],
            'domain'   => $this->filters['domain'],
            'firstName'=> $this->filters['firstname'],
            'lastName' => $this->filters['lastname'],
            'email'    => $this->filters['email'],
            'owner'    => intval($this->filters['owner']),
            'customer' => intval($this->filters['customer']),
            'reseller' => intval($this->filters['reseller']),
            'groups'   => array($this->filters['group'])
        );

        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';

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
            'filter'  => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        // Insert credentials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAccounts');

        // Call function
        $result = $this->SoapEngine->soapclient->getAccounts($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $this->rows = $result->total;

            if ($this->rows && $action != 'PerformActions' && $action != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <p>
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-condensed table-striped'width=100%>
            <thead>
            <tr>
                <th>Id</th>
                <th>SIP account";
            $this->showSortCaret('username');
            if ($this->sorting['sortBy'] == 'domain') {
                print " (domain ";
                $this->showSortCaret('domain');
                print ")";
            }
            print "</th>
                <th>Full name</th>
                <th>Email address</th>
                <th>Timezone</th>
                <th align=right>Capacity</th>
                <th align=right>Quota</th>
                <th align=right>Balance</th>
                <th>Owner</th>
                <th>Change date";
            $this->showSortCaret('changeDate');
            print "</th>
                <th>Actions</th>
            </tr>
            </thead>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage) {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows = $this->rows;
            }


            if ($this->rows) {
                $i=0;

                $_prepaid_accounts = array();
                while ($i < $maxrows) {
                    if (!$result->accounts[$i]) break;
                    $account = $result->accounts[$i];
                    if ($account->prepaid) {
                        $_prepaid_accounts[] = array(
                            "username" => $account->id->username,
                            "domain" => $account->id->domain
                        );
                    }
                    $i++;
                }

                if (count($_prepaid_accounts)) {
                    // Insert credetials
                    $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                    $this->log_action('getPrepaidStatus');

                    // Call function
                    $result1 = $this->SoapEngine->soapclient->getPrepaidStatus($_prepaid_accounts);
                    if (!(new PEAR)->isError($result1)) {
                        $j=0;

                        foreach ($result1 as $_account) {
                            $_sip_account = sprintf(
                                "%s@%s",
                                $_prepaid_accounts[$j]['username'],
                                $_prepaid_accounts[$j]['domain']
                            );
                            $_prepaid_balance[$_sip_account] = $_account->balance;
                            $j++;
                        }
                    }
                }

                $i=0;

                while ($i < $maxrows) {
                    if (!$result->accounts[$i]) break;

                    $account = $result->accounts[$i];

                    $index = $this->next+$i+1;

                    $deleteUrl = array(
                        'service' => $this->SoapEngine->service,
                        'action' => 'Delete',
                        'key' => $account->id->username
                    );

                    if (!$this->filters['domain']) {
                        $deleteUrl['domain_filter'] = $account->id->domain;
                    }

                    if (!$this->filters['username']) {
                        $deleteUrl['username_filter'] = $account->id->username;
                    }

                    if ($action == 'Delete' &&
                        $_REQUEST['key'] == $account->id->username &&
                        $_REQUEST['domain_filter'] == $account->id->domain) {
                        $deleteUrl['confirm'] = 1;
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    if ($account->reseller) {
                        $reseller_sip_settings_page = $account->reseller;
                    } else if ($this->SoapEngine->impersonate) {
                        // use the reseller from the soap engine
                        $reseller_sip_settings_page = $this->SoapEngine->impersonate;
                    } else {
                        // use the reseller from the login
                        $reseller_sip_settings_page = $this->reseller;
                    }

                    if ($this->sip_settings_page) {
                        $settingsUrl = array(
                            'account' => sprintf('%s@%s', $account->id->username, $account->id->domain),
                            'sip_engine' => $this->SoapEngine->sip_engine
                        );

                        if ($this->adminonly) {
                            $settingsUrl['reseller'] = $reseller_sip_settings_page;
                            $settingsUrl['adminonly'] = $this->adminonly;
                        } else {
                        	if ($account->reseller == $this->reseller) $settingsUrl['reseller'] = $reseller_sip_settings_page;
                        }

                        foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
                            if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;
                            $settingsUrl[$element] = $this->SoapEngine->extraFormElements[$element];
                        }

                        $sip_account = sprintf(
                            "<a href=\"javascript:void(null);\" onClick=\"return window.open('%s&%s', 'SIP_Settings',
                            'toolbar=1,status=1,menubar=1,scrollbars=1,resizable=1,width=800,height=720')\">
                            %s@%s</a>",
                            $this->sip_settings_page,
                            http_build_query($settingsUrl),
                            $account->id->username,
                            $account->id->domain
                        );
                    } else {
                        $sip_account = sprintf("%s@%s", $account->id->username, $account->id->domain);
                    }

                    /*
                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($account->customer));
                    */

                    if ($account->owner) {
                        $ownersUrlData = array(
                            'service' => sprintf('customers@%s', $this->SoapEngine->soapEngine),
                            'customer_filter' => $account->owner
                        );
                        $_owner_url = sprintf(
                            '<a href="%s&%s">%s</a>',
                            $this->url,
                            http_build_query($ownersUrlData),
                            $account->owner
                        );
                    } else {
                        $_owner_url='';
                    }
                    $prepaid_account = sprintf("%s@%s", $account->id->username, $account->id->domain);

                    if ($account->callLimit) {
                        $callLimit = $account->callLimit;
                    } elseif ($this->platform_call_limit) {
                        $callLimit = $this->platform_call_limit;
                    } else {
                        $callLimit = '';
                    }

                    printf(
                        '
                        <tr>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s %s</td>
                        <td><a href=mailto:%s>%s</a></td>
                        <td align=right>%s</td>
                        <td align=right>%s</td>
                        <td align=right>%s</td>
                        <td align=right>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a class="btn-small btn-danger" href="%s">%s</a></td>
                        </tr>
                        ',
                        $index,
                        $sip_account,
                        $account->firstName,
                        $account->lastName,
                        $account->email,
                        $account->email,
                        $account->timezone,
                        $callLimit,
                        $account->quota,
                        $_prepaid_balance[$prepaid_account],
                        $_owner_url,
                        $account->changeDate,
                        $this->url.'&'.$this->addFiltersToURL().'&'.http_build_query($deleteUrl),
                        $actionText
                    );

                    $i++;
                }
            }

            print "</table>";

            $this->showPagination($maxrows);

            return true;
        }
    }

    function showSeachFormCustom()
    {
        printf(
            "<div class='input-prepend'><span class='add-on'>Account</span><input class=span2 type=text name=username_filter value='%s'></div>",
            $this->filters['username']
        );
        print "@";

        if (count($this->allowedDomains) > 0) {
            if ($this->filters['domain'] && !in_array($this->filters['domain'], $this->allowedDomains)) {
                printf("<input class=span2 type=text name=domain_filter value='%s'>", $this->filters['domain']);
            } else {
                $selected_domain[$this->filters['domain']]='selected';
                printf("<select class=span2 name=domain_filter>
                    <option>
                 ");
                foreach ($this->allowedDomains as $_domain) {
                    printf("<option value='$_domain' %s>$_domain\n", $selected_domain[$_domain]);
                }
                printf("</select>\n");
            }
        } else {
            printf("<input class=span1 type=text size=15 name=domain_filter value='%s'>", $this->filters['domain']);
        }

        printf("<div class='input-prepend'><span class='add-on'>FN</span><input class=span1 type=text name=firstname_filter value='%s'></div>\n", $this->filters['firstname']);
        printf("<div class='input-prepend'><span class='add-on'>LN</span><input class=span1 type=text name=lastname_filter value='%s'></div>\n", $this->filters['lastname']);
        printf("<div class='input-prepend'><span class='add-on'>Email</span><input class=span2 type=text name=email_filter value='%s'></div>\n", $this->filters['email']);
        printf("<div class='input-prepend'><span class='add-on'>Owner</span><input class=span1 type=text name=owner_filter value='%s'></div>\n", $this->filters['owner']);

        $selected_group[$this->filters['group']] = 'selected';
        print "<select class=span2 name=group_filter><option value=''>Feature...";
        foreach (array_keys($this->group_filter_list) as $key) {
            if (!$this->getResellerProperty('pstn_access')) {
                if ($key == 'free-pstn' or $key == 'prepaid' or $key == 'quota') {
                    continue;
                }
            }
            printf("<option  value=%s %s>%s", $key, $selected_group[$key], $this->group_filter_list[$key]);
        }
        print "</select>";
    }

    function deleteRecord($dictionary = array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['username']) {
            $username = $dictionary['username'];
        } else {
            $username = $_REQUEST['key'];
        }

        if ($dictionary['domain']) {
            $domain = $dictionary['domain'];
        } else {
            $domain = $this->filters['domain'];
        }

        if (!strlen($username) || !strlen($domain)) {
            print "<p><font color=red>Error: missing SIP account username or domain. </font>";
            return false;
        }

        $account = array(
            'username' => $username,
            'domain'   => $domain
        );

        $function = array(
            'commit'   => array(
                'name'       => 'deleteAccount',
                'parameters' => array($account),
                'logs'       => array('success' => sprintf('SIP account %s@%s has been deleted',$_REQUEST['key'], $this->filters['domain'])
                )
            )
        );

        foreach (array_keys($this->filters) as $_filter) {
            if ($_filter == 'username' || $_filter == 'domain') continue;
            $new_filters[$_filter] = $this->filters[$_filter];
        }

        $this->filters = $new_filters;

        return $this->SoapEngine->execute($function, $this->html);
    }

    function showAddForm()
    {
        if ($this->filters['username']) return;

        if (!count($this->allowedDomains)) {
            print "<div class=\"alert alert-error\">You must create at least one SIP domain before adding SIP accounts</div>";
            return false;
        }

        printf("<form class='form-inline' method=post name=addform action=%s>", $_SERVER['PHP_SELF']);

        print "
        <div class='well well-small'>
         ";
        print "
        <input class='btn btn-warning' type=submit name=action value=Add>
        ";

        if ($_REQUEST['account']) {
            $_account = $_REQUEST['account'];
        } else {
            $_account = $this->getCustomerProperty('sip_accounts_last_username');
        }

        printf("<div class=input-prepend><span class='add-on'>Account</span><input class=span2 type=text size=15 name=account value='%s'></div>",$_account);

        if ($_REQUEST['domain']) {
            $_domain = $_REQUEST['domain'];
            $selected_domain[$_REQUEST['domain']]='selected';
        } else if ($this->filters['domain']) {
            $_domain = $this->filters['domain'];
            $selected_domain[$this->filters['domain']]='selected';
        } else if ($_domain = $this->getCustomerProperty('sip_accounts_last_domain')) {
            $selected_domain[$_domain]='selected';
        }

        if (count($this->allowedDomains) > 0) {
            print "@<select class=span2 name=domain>";
            foreach ($this->allowedDomains as $_domain) {
                printf("<option value='%s' %s>%s\n", $_domain, $selected_domain[$_domain], $_domain);
            }
            print "</select>";
        } else {
            printf("<input type=text name=domain class=span2value='%s'>", $_domain);
        }

        if ($_REQUEST['quota']) {
            $_quota = $_REQUEST['quota'];
        } else {
            $_quota = $this->getCustomerProperty('sip_accounts_last_quota');
        }

        if (!$_quota) $_quota='';

        if ($_prepaid = $this->getCustomerProperty('sip_accounts_last_prepaid')) {
            $checked_prepaid='checked';
        } else {
            $checked_prepaid='';
        }

        if ($_pstn = $this->getCustomerProperty('sip_accounts_last_pstn')) {
            $checked_pstn='checked';
        } else {
            $checked_pstn='';
        }

        printf(" <div class=input-prepend><span class='add-on'>Password</span><input class=span1 type=password size=10 name=password value='%s' autocomplete='off'></div>",$_REQUEST['password']);
        printf(" <div class=input-prepend><span class='add-on'>Name</span><input class=span2 type=text size=15 name=fullname value='%s' autocomplete='off'></div>",$_REQUEST['fullname']);
        printf(" <div class=input-prepend><span class='add-on'>Email</span><input class=span2 type=text size=20 name=email value='%s' autocomplete='off'></div>",$_REQUEST['email']);
        printf(" <div class=input-prepend><span class='add-on'><nobr>Owner</span><input class=span1 type=text size=7 name=owner value='%s'></nobr></div> ",$_REQUEST['owner']);
        if ($this->getResellerProperty('pstn_access')) {
            printf(" PSTN <input type=checkbox class=checkbox name=pstn value=1 %s></nobr>", $checked_pstn);
            printf(" <div class=input-prepend><span class='add-on'><nobr>Quota</span><input class=span1  type=text size=5 name=quota value='%s'></nobr></div>",$_quota);
            if ($this->prepaidChangesAllowed()) {
                printf(" <nobr>Prepaid <input class=checkbox type=checkbox name=prepaid value=1 %s></nobr> ",$checked_prepaid);
            } else {
                printf(" <nobr>Prepaid <input class=checkbox type=checkbox name=prepaid value=1 checked disabled=true></nobr> ");
            }
        }

        $this->printHiddenFormElements();

        print "</div>
        </form>
        ";
    }

    function addRecord($dictionary = array())
    {
        dprint_r($dictionary);

        if ($dictionary['account']) {
            $account_els  = explode("@", $dictionary['account']);
            $this->skipSaveProperties = true;
        } else {
            $account_els  = explode("@", trim($_REQUEST['account']));
        }

        list($customer, $reseller) = $this->customerFromLogin($dictionary);

        $username = $account_els[0];

        if (strlen($account_els[1])) {
            $domain = $account_els[1];
        } else if ($dictionary['domain']) {
            $domain = $dictionary['domain'];
        } else if ($_REQUEST['domain']) {
            $domain = trim($_REQUEST['domain']);
        } else {
            printf("<p><font color=red>Error: Missing SIP domain</font>");
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
            $firstName = $name_els[0];
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
                $lastName = $username;
            }
        }

        $lastName = trim($lastName);

        if (strlen($dictionary['timezone'])) {
            $timezone = $dictionary['timezone'];
        } elseif (strlen(trim($_REQUEST['timezone']))) {
            $timezone = trim($_REQUEST['timezone']);
        } elseif ($this->SoapEngine->default_timezone) {
            $timezone = $this->SoapEngine->default_timezone;
        } else {
            $timezone='Europe/Amsterdam';
        }

        if (!in_array($timezone, $this->timezones)) {
            $timezone='Europe/Amsterdam';
        }

        if (strlen($dictionary['password'])) {
            $password = $dictionary['password'];
        } elseif (strlen(trim($_REQUEST['password']))) {
            $password = trim($_REQUEST['password']);
        } else {
            $password = $this->RandomString(10);
        }

        if (is_array($dictionary['groups'])) {
            $groups = $dictionary['groups'];
        } else {
            $groups = array();
        }

        if (is_array($dictionary['ip_access_list'])) {
            $ip_access_list = $dictionary['ip_access_list'];
        } else {
            $ip_access_list = array();
        }

        if (strlen($dictionary['call_limit'])) {
            $call_limit = $dictionary['call_limit'];
        } else {
            $call_limit = $_REQUEST['call_limit'];
        }

        if ($dictionary['pstn'] || $_REQUEST['pstn']) {
            $_pstn=1;
            $groups[]='free-pstn';
        } else {
            $_pstn=0;
        }

        if (strlen($dictionary['email'])) {
            $email = $dictionary['email'];
        } else {
            $email = trim($_REQUEST['email']);
        }

        if (strlen($dictionary['rpid'])) {
            $rpid = $dictionary['rpid'];
        } else {
            $rpid = trim($_REQUEST['rpid']);
        }

        if (strlen($dictionary['owner'])) {
            $owner = intval($dictionary['owner']);
        } else {
            $owner = intval($_REQUEST['owner']);
        }

        if (!$owner) {
            $owner = intval($customer);
        }

        if (strlen($dictionary['quota'])) {
            $quota = intval($dictionary['quota']);
        } else {
            $quota = intval($_REQUEST['quota']);
        }

        if ($this->prepaidChangesAllowed()) {
            if (strlen($dictionary['prepaid'])) {
                $prepaid = intval($dictionary['prepaid']);
            } else {
                $prepaid = intval($_REQUEST['prepaid']);
            }
        } else {
            $prepaid = 1;
        }

        if ($prepaid) {
            $groups[]='prepaid';
        }

        if (!$email) {
            if ($username=="<autoincrement>") {
                $email='unknown@'.strtolower($domain);
            } else {
                $email = strtolower($username).'@'.strtolower($domain);
            }
        }

        if (!$this->skipSaveProperties) {
            $_p = array(
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
            $properties = $dictionary['properties'];
        } else {
            $properties = array();
        }

        if ($this->SoapEngine->login_credentials['reseller']) {
            $reseller_properties = $this->getResellerProperties($this->SoapEngine->login_credentials['reseller'],'store_clear_text_passwords');

            if (strlen($reseller_properties['store_clear_text_passwords'])) {
                $this->store_clear_text_passwords = $reseller_properties['store_clear_text_passwords'];
            }
        } else {
            $_reseller = $this->getResellerForDomain(strtolower($domain));

            if ($_reseller) {
                $reseller_properties = $this->getResellerProperties($_reseller, 'store_clear_text_passwords');

                if (strlen($reseller_properties['store_clear_text_passwords'])) {
                    $this->store_clear_text_passwords = $reseller_properties['store_clear_text_passwords'];
                }
            }
        }

        if ($this->store_clear_text_passwords || $username == '<autoincrement>') {
            $password_final = $password;
        } else {
            $md1=strtolower($username).':'.strtolower($domain).':'.$password;
            $md2=strtolower($username).'@'.strtolower($domain).':'.strtolower($domain).':'.$password;
            $password_final = md5($md1).':'.md5($md2);
        }

        $account = array(
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
                     'acl'        => $ip_access_list,
                     'properties' => $properties
                     );

        if (isset($call_limit)) {
            $account['callLimit'] = intval($call_limit);
        }

        //print_r($account);
        $deleteAccount = array('username' => $username,
                             'domain'   => $domain);


        if ($this->html) {
            if ($username == '<autoincrement>') {
                $success_log = sprintf('SIP account has been generated in domain %s', $domain);
            } else {
                $success_log = sprintf('SIP account %s@%s has been added', $username, $domain);
            }
        }

        $function = array('commit'   => array('name'       => 'addAccount',
                                            'parameters' => array($account),
                                            'logs'       => array('success' => $success_log))
                        );


            return $this->SoapEngine->execute($function, $this->html);
    }

    function getAllowedDomains()
    {
        // Filter
        $filter = array(
            'domain'    => ''
        );

        // Range
        $range = array(
            'start' => 0,
            'count' => 750
        );

        $orderBy = array(
            'attribute' => 'domain',
            'direction' => 'ASC'
        );

        // Compose query
        $Query = array(
            'filter'  => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getDomains');
        $result = $this->SoapEngine->soapclient->getDomains($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            printf(
                "<p><font color=red>Error in getAllowedDomains from %s: %s (%s): %s</font>",
                $this->SoapEngine->SOAPurl,
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            //return false;
        } else {
            foreach ($result->domains as $_domain) {
                if ($this->validDomain($_domain->domain)) {
                    $this->allowedDomains[] = $_domain->domain;
                }
            }
        }
    }

    function showPasswordReminderForm($accounts = array())
    {
        print "<div class=row-fluid><div id=wrapper2><div class=\"page-header\"><h2>";

        print _("Login account reminder");
        print "</h2></div>
            <form class=form-inline method=post>";

        print _("<p>Fill in the e-mail address used during the registration of the SIP account:</p>");
        printf(
            "<input type=text size=35 name='email_filter' value='%s' placeholder='",
            $this->filters['email']
        );
        print _("Email address");
        print "'>";
        if (count($accounts) > 1 || $_REQUEST['sip_filter']) {
            print "<br /><br /><div class=\"alert alert-warning\"><strong>";
            print _("Warning");
            print "</strong> ";
            print _("More than one account uses this email address. If you wish to receive the password for a particular account fill in the SIP account below, default it has been send it to the first 5 accounts found");
            print "</div>";

            printf(
                "<input type=text size=35 name='sip_filter' value='%s'>",
                htmlspecialchars($_REQUEST['sip_filter'])
            );
        }

        print "<input class='btn btn-primary' type=submit value='Submit'></form>";
    }

    function showPasswordReminderUpdateFormEncrypted($id, $account)
    {
        if ($account) {
            print "<div class=row-fluid><div id='wrapper2'><div class=\"page-header\"><h2>";

            print _("Update passwords");
            print "<small>";
            print " (for ";
            print $account;
            print ")</small></h2></div><div class=row-fluid>";

            print _("<p>Please choose new passwords for your account, if you leave them empty no change will be performed</p>");

            print "</div><div>";
            print "<form class='form-horizontal' method=post>";
            print "<div class=control-group>";
            print "<label class=control-label>";
            print _("SIP Account Password");
            print "</label>";
            print "<div class=controls><div class='input-prepend'>";
            print "<span class=\"add-on\"><i class=\"icon-key\"></i></span>";
            print "<input size=35 name='sip_password' type='password' value=''>";
            print "</div></div></div>";

            print "<div class=control-group>";
            print "<label class=control-label>";
            print _("Web Password");
            print "</label>";
            print "<div class='controls'><div class='input-prepend'>";
            print "<span class=\"add-on\"><i class=\"icon-key\"></i></span>";
            print "<input rel='popover' title='' data-original-title='Web password' data-trigger='focus' data-toggle='popover' data-content='";
            print _("<strong>Optional</strong> password to allow access to the SIP settings page");
            print "'type=text size=35 name='web_password' type='password' value=''>";
            print "</div></div></div>";
            print "  <div class=\"control-group\"><div class=\"controls\">";
            print "
                <button class='btn btn-primary' type=submit>Submit</button></div></div>
            </form></div>
            ";
        }
    }

    function showPasswordReminderFormEncrypted($accounts = array())
    {
        print "<div class=row-fluid><div id=wrapper2><div class=\"page-header\"><h2>";

        print _("Sip Account Reminder/Password Reset");
        print "</h2></div><div class=row-fluid>
            <form class='form-reminder' method=post>";

        //print _("<p>Please fill in the SIP account and e-mail address used during the registration of the SIP account to receive a login reminder and a possiblity to reset your passwords.</p>");

        if (count($accounts) < 1 && $_REQUEST['sip_filter'] && $_REQUEST['email_filter']) {
            print "<div class=\"alert alert-error\"><strong>";
            print _("Error");
            print "</strong><br /> ";
            print _("The email adress does not match email address in the SIP account, or the SIP account does not exist.");
            print "<br/>";
            print _("An email has not been sent.");
            print "</div>";
        } elseif (count($accounts) < 1 && $_REQUEST['email_filter']) {
            print "<div class=\"alert alert-error\"><strong>";
            print _("Error");
            print "</strong>: ";
            print _("The email adress does not match the email address in any SIP account.");
            print "<br/>";
            print _("An email has not been sent.");
            print "</div>";
        }
        print "
            <input rel='popover' title='' data-original-title='SIP Account' data-trigger='focus' data-toggle='popover' data-content='";
        print _("If known, please fill in the SIP account name to receive a password reminder");
        printf(
            "' type=text size=35 class='input-block-level' name='sip_filter' value='%s' placeholder='",
            htmlspecialchars($_REQUEST['sip_filter'])
        );
        print _("SIP Account");
        print "'>";
        print "<input rel='popover' title='' data-original-title='Email address' data-trigger='focus' data-toggle='popover' data-content='";
        print _("Please fill in the e-mail address used during the registration of the SIP account ");
        printf(
            "' type=text size=35 name='email_filter' class='input-block-level' value='%s' placeholder='",
            $this->filters['email']
        );
        print _("Email Address");
        print "'>";
        print "<input type='hidden' name='password_reset' value='on'>";
        print "<center><button id='submit' class='btn btn-primary' type=submit>Send Reminder</button></center></form></div>";

        if (count($accounts) < 1 && $_REQUEST['sip_filter']) {
            print "<script type=\"text/javascript\">
            //$(document).ready(function () {
                $('[name=email_filter]').focus();
                $('[name=email_filter]').popover('show');
                //console.log($('[name=email_filter]').val);
            //}
            </script>";
        }
    }

    function getAccountsForPasswordReminderEncrypted($maximum_accounts = 5)
    {
        $accounts = array();

        //$filter  = array('email' => $this->filters['email']);

        if ($_REQUEST['sip_filter']) {
            list($username, $domain) = explode('@', trim($_REQUEST['sip_filter']));
            if ($username && $domain) {
                $filter = array(
                    'username' => $username,
                    'domain' => $domain,
                    'email' => $this->filters['email']
                );
            }
        } else {
            $filter = array('email' => $this->filters['email']);
        }

        $range = array(
            'start' => 0,
            'count' => $maximum_accounts
        );

        $orderBy = array(
            'attribute' => 'changeDate',
            'direction' => 'DESC'
        );

        $Query = array(
            'filter'  => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAccounts');
        $result = $this->SoapEngine->soapclient->getAccounts($Query);

        if (!$this->checkLogSoapError($result, true)) {
            $i = 0;

            while ($i < $result->total) {
                if (!$result->accounts[$i]) break;

                $account = $result->accounts[$i];
                $accounts[] = array('username'=> $account->id->username,
                    'domain'  => $account->id->domain
                );
                $i++;
            }
        }

        return $accounts;
    }

    function getAccountsForPasswordReminder($maximum_accounts = 5)
    {
        $accounts = array();

        $filter  = array('email' => $this->filters['email']);

        if ($_REQUEST['sip_filter']) {
            list($username, $domain) = explode('@', trim($_REQUEST['sip_filter']));
            if ($username && $domain) {
                $filter  = array( 'username' => $username,
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
        $this->log_action('getAccounts');
        $result  = $this->SoapEngine->soapclient->getAccounts($Query);

        if (!$this->checkLogSoapError($result, true)) {
            $i=0;

            while ($i < $result->total) {
            	if (!$result->accounts[$i]) break;
                $account = $result->accounts[$i];
                $accounts[] = array(
                    'username'=> $account->id->username,
                    'domain'  => $account->id->domain
                );
                $i++;
            }
        }

        return $accounts;
    }

    function getResellerForDomain($domain='')
    {
        // Filter
        $filter = array(
                      'domain'    => $domain
                      );

        // Range
        $range = array('start' => 0,
                     'count' => 1
                     );

        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
                         );

        // Compose query
        $Query = array('filter'     => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getDomains');

        // Call function
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if ($result->domains[0]) {
                return $result->domains[0]->reseller;
            } else {
                return false;
            }
        }
    }

    function getResellerProperties($reseller = '', $property = '')
    {
        $properties = array();

        if (!$this->SoapEngine->customer_engine) {
            dprint("No customer_engine available");
            return true;
        }

        if (!$reseller) {
            dprint("No customer provided");
            return true;
        }

        if (!$property) {
            dprint("No property provided");
            return true;
        }

        $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuthCustomers);
        $result = $this->SoapEngine->soapclientCustomers->getProperties(intval($reseller));

        dprint_r($result);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        }

        foreach ($result as $_property) {
            $properties[$_property->name] = $_property->value;
        }

        return $properties;
    }

    function pstnChangesAllowed()
    {
        //dprint_r($this->loginProperties);
        $_customer_pstn_changes = $this->getCustomerProperty('pstn_changes');
        $_reseller_pstn_changes = $this->getCustomerProperty('pstn_changes');

        if ($this->adminonly) {
            return true;
        } elseif ($this->customer == $this->reseller && $_reseller_pstn_changes) {
            return true;
        } elseif ($this->loginImpersonate == $this->reseller && $_reseller_pstn_changes) {
            return true;
        } elseif ($_reseller_pstn_changes && $_customer_pstn_changes) {
            return true;
        }

        return false;
    }

    function prepaidChangesAllowed()
    {
        //dprint_r($this->loginProperties);
        $_customer_prepaid_changes = $this->getCustomerProperty('prepaid_changes');
        $_reseller_prepaid_changes = $this->getCustomerProperty('prepaid_changes');

        if ($this->adminonly) {
            return true;
        } elseif ($this->customer == $this->reseller && $_reseller_prepaid_changes) {
            return true;
        } elseif ($this->loginImpersonate == $this->reseller && $_reseller_prepaid_changes) {
            return true;
        } elseif ($_reseller_prepaid_changes && $_customer_prepaid_changes) {
            return true;
        }

        return false;
    }

    function getTimezones()
    {
        $this->timezones = array();
        if (!$fp = fopen("timezones", "r")) {
            print _("Failed to open timezone file.");
            return false;
        }
        while ($buffer = fgets($fp, 1024)) {
            $this->timezones[] = trim($buffer);
        }
        fclose($fp);
    }

    function showTextBeforeCustomerSelection()
    {
        print _("Domain owner");
    }
}
