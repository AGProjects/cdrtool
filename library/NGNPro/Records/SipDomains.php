<?php

class SipDomains extends Records
{
    var $FieldsAdminOnly = array(
        'reseller' => array('type'=>'integer'),
    );

    var $Fields = array(
        'customer'    => array('type'=>'integer'),
        'certificate' =>  array('type'=>'text'),
        'private_key' =>  array('type'=>'text'),
        'match_ip_address' =>  array('type'=>'text', 'name'=> 'Match IP addresses'),
        'match_sip_domain' =>  array('type'=>'text', 'name'=> 'Match SIP Domain'),
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

        if ($this->checkLogSoapError($result, true)) {
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
                $maxrows = $this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows) {
                    if (!$result->domains[$i]) break;
                    $domain = $result->domains[$i];

                    $index = $this->next+$i+1;

                    $base_url_data = array(
                        'service' => $this->SoapEngine->service,
                        'domain_filter' => $domain->domain
                    );

                    $delete_url_data = array_merge(
                        $base_url_data,
                        array(
                            'action' => 'Delete'
                        )
                    );

                    $customer_url_data = array(
                        'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                        'customer_filter' => $domain->customer
                    );

                    $sip_domains_url_data = array_merge(
                        $base_url_data,
                        array(
                            'service' => sprintf('sip_domains@%s', $this->SoapEngine->soapEngine)
                        )
                    );

                    $sip_accounts_url_data = array_merge(
                        $base_url_data,
                        array(
                            'service' => sprintf('sip_accounts@%s', $this->SoapEngine->soapEngine)
                        ),
                    );

                    $sip_aliasses_url_data = array(
                        'service' => sprintf('sip_aliases@%s', $this->SoapEngine->soapEngine),
                        'alias_domain_filter' => $domain->domain,
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['domain_filter'] == $domain->domain) {
                        $delete_url_data['confirm'] = 1;
                        $deleteText = "<font color=red>Confirm</font>";
                    } else {
                        $deleteText = "Delete";
                    }

                    $delete_url = $this->buildUrl($delete_url_data);
                    $_customer_url = $this->buildUrl($customer_url_data);
                    $_sip_domains_url = $this->buildUrl($sip_domains_url_data);
                    $_sip_accounts_url = $this->buildUrl($sip_accounts_url_data);
                    $_sip_aliases_url = $this->buildUrl($sip_aliasses_url_data);

                    if ($this->adminonly) {
                        $export_url_data = array_merge(
                            $base_url_data,
                            array(
                                'action' => 'Export'
                            )
                        );
                        $export_url = $this->buildUrl($export_url_data);

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
        printf(
            "
            <div class='input-prepend'>
            <span class=add-on>SIP domain</span>
            <input class=span2 type=text size=20 name=domain_filter value='%s'>
            </div>
            ",
            $this->filters['domain']
        );
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
            $domain = $dictionary['domain'];
        } else {
            $domain = $this->filters['domain'];
        }

        if (!strlen($domain)) {
            print "<p><font color=red>Error: missing SIP domain. </font>";
            return false;
        }

        $function = array('commit'   => array('name'       => 'deleteDomain',
                                            'parameters' => array($domain),
                                            'logs'       => array('success' => sprintf('SIP domain %s has been deleted', $domain))
                                           )
                                           );

        unset($this->filters);
        return $this->SoapEngine->execute($function, $this->html);
    }

    function showAddForm()
    {
        if ($this->selectionActive) return;
            printf("<form class=form-inline method=post name=addform action=%s enctype='multipart/form-data'>", $_SERVER['PHP_SELF']);
            print <<< END
<div class='well well-small'>
    <input type=submit class='btn btn-warning' name=action value=Add>
END;

            $this->showCustomerTextBox();

            print <<< END
</div>
<div class='input-prepend'>
    <span class='add-on'>SIP domain</span>
    <input type=text size=20 name=domain>
</div>
END;
            $this->printHiddenFormElements();

            print <<< END
    Import SIP domain from file:
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
            <button type='submit' name=action class='btn fileupload-exists' value="Add">
                <i class='icon-upload'></i> Import
            </button>
        </div>
    </div>
</div>
</form>
END;
    }

    function addRecord($dictionary = array())
    {
        if ($this->adminonly && $_FILES['import_file']['tmp_name']) {
            $content = fread(fopen($_FILES['import_file']['tmp_name'], "r"), $_FILES['import_file']['size']);
            //print_r($content);

            if (!$imported_data = json_decode($content, true)) {
                printf("<p><font color=red>Error: reading imported data. </font>");
                return false;
            }

            //print_r($imported_data);

            if (!in_array('sip_domains', array_keys($imported_data))) {
                printf("<p><font color=red>Error: Missing SIP domains in imported data. </font>");
                return false;
            }

            if (!in_array('sip_accounts', array_keys($imported_data))) {
                return false;
                printf("<p><font color=red>Error: Missing SIP accounts in imported data. </font>");
            }

            foreach ($imported_data['customers'] as $customer) {
                // Insert credetials
                $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addAccount');

                $customer['credit'] = floatval($customer['credit']);
                $customer['balance'] = floatval($customer['balance']);
                // Call function
                $result     = $this->SoapEngine->soapclientCustomers->addAccount($customer);
                if ($this->soapHasError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 5001) {
                        $result = $this->SoapEngine->soapclientCustomers->updateCustomer($customer);
                        if (!$this->checkLogSoapError($result, true, true)) {
                            printf('<p>Customer %s has been updated', $customer['id']);
                        }
                    } else {
                        $log = sprintf(
                            "SOAP request error from %s: %s (%s): %s",
                            $this->SoapEngine->SOAPurl,
                            $error_msg,
                            $error_fault->detail->exception->errorcode,
                            $error_fault->detail->exception->errorstring
                        );
                        printf("<p><font color=red>Error: $log</font>");
                    }
                } else {
                    printf('<p>Customer %s has been added', $customer['id']);
                }
            }

            foreach ($imported_data['sip_domains'] as $domain) {
                flush();
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addDomain');
                $result = $this->SoapEngine->soapclient->addDomain($domain);
                if ($this->soapHasError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 1001) {
                        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                        $this->log_action('updateDomain');
                        $result = $this->SoapEngine->soapclient->updateDomain($domain);
                        if (!$this->checkLogSoapError($result, true, true)) {
                             printf('<p>SIP domain %s has been updated', $domain['domain']);
                        }
                    } else {
                        $log = sprintf(
                            "SOAP request error from %s: %s (%s): %s",
                            $this->SoapEngine->SOAPurl,
                            $error_msg,
                            $error_fault->detail->exception->errorcode,
                            $error_fault->detail->exception->errorstring
                        );
                        printf("<p><font color=red>Error: $log</font>");
                    }
                } else {
                    printf('<p>SIP domain %s has been added', $domain['domain']);
                }
            }
            $i = 0;
            $added = 0;
            $updated = 0;
            $failed = 0;
            foreach ($imported_data['sip_accounts'] as $account) {
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

                if ($this->soapHasError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 1011) {
                        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                        $result = $this->SoapEngine->soapclient->updateAccount($account);
                        if ($this->checkLogSoapError($result, true, true)) {
                            $failed += 1;
                        } else {
                            printf(
                                '<p>%d SIP account %s@%s has been updated</p>',
                                $i,
                                $account['id']['username'],
                                $account['id']['domain']
                            );
                            $updated += 1;
                        }
                    } else {
                        $log = sprintf(
                            "SOAP request error from %s: %s (%s): %s",
                            $this->SoapEngine->SOAPurl,
                            $error_msg,
                            $error_fault->detail->exception->errorcode,
                            $error_fault->detail->exception->errorstring
                        );
                        printf("<p><font color=red>Error: $log</font>");
                        $failed += 1;
                    }
                } else {
                    printf(
                        '<p>%d SIP account %s@%s has been added',
                        $i,
                        $account['id']['username'],
                        $account['id']['domain']
                    );
                    $added += 1;
                }
            }
            if ($added) {
                printf('<p>%d SIP accounts added', $added);
            }
            if ($updated) {
                printf('<p>%d SIP accounts updated', $updated);
            }
            if ($failed) {
                printf('<p>%d SIP accounts failed', $failed);
            }

            $added = 0;
            foreach ($imported_data['sip_aliases'] as $alias) {
                flush();

                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addAlias');
                $result = $this->SoapEngine->soapclient->addAlias($alias);

                if (!$this->checkLogSoapError($result, true, true)) {
                    $added += 1;
                }
            }

            if ($added) {
                printf('<p>%d SIP aliases added', $added);
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
                print "<font color = red>Error: invalid domain name</font>";
                return false;
            }

            $domainStructure = array(
                'domain'   => strtolower($domain),
                'customer' => intval($customer),
                'reseller' => intval($reseller)
            );
            $function = array(
                'commit'   => array(
                    'name'       => 'addDomain',
                    'parameters' => array($domainStructure),
                    'logs'       => array('success' => sprintf('SIP domain %s has been added', $domain))
                )
            );

            return $this->SoapEngine->execute($function, $this->html);
        }
    }

    function getRecordKeys()
    {
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

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getDomains');
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ($this->soapHasError($result)) {
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
                $this->selectionKeys[] = $_domain->domain;
            }
        }
    }

    function getRecord($domain)
    {
        // Filter
        $filter = array(
            'domain'    => $domain
        );

        // Range
        $range = array(
            'start' => 0,
            'count' => 1
        );

        $orderBy = array(
            'attribute' => 'changeDate',
            'direction' => 'DESC'
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
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ($this->checkLogSoapError($result, true)) {
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
            $pemdata = sprintf("%s\n%s", $domain->certificate, $domain->private_key);
            $cert = openssl_x509_read($pemdata);
            if ($cert) {
                $cert_data = openssl_x509_parse($cert);
                openssl_x509_free($cert);
                $expire = mktime($cert_data['validTo_time_t']);
            } else {
                $cert_data = "";
            }
        }

        #print("<pre>");
        #print_r($cert_data);
        #print("</pre>");
        print <<< END
<table border=0 cellpadding=10>
    <tr>
        <td valign=top>
        <table border=0>
END;
        printf("<form method=post name=addform action=%s>", $_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        print "<tr>
        <td colspan=2><input type=submit value=Update>
        </td></tr>";

        if ($cert_data) {
            // Parse the resource and print out the contents.
            $ts = $cert_data['validTo_time_t'];
            $expire = new DateTime("@$ts");
            printf(
                "<tr><td>TLS CN</td><td>%s</td></tr>",
                $cert_data['subject']['CN']
            );
            printf(
                "<tr><td>CA Issuer</td><td>%s %s %s</td></tr>",
                $cert_data['issuer']['C'],
                $cert_data['issuer']['O'],
                $cert_data['issuer']['CN']
            );
            printf("<tr><td>Expire date</td><td>%s</td></tr>", $expire->format('Y-m-d'));
        }
        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                if ($this->FieldsAdminOnly[$item]['name']) {
                    $item_name = $this->FieldsAdminOnly[$item]['name'];
                } else {
                    $item_name = preg_replace("/_/", " ", ucfirst($item));
                }

                if ($this->FieldsAdminOnly[$item]['type'] == 'text') {
                    printf(
                        "
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                        </tr>",
                        $item_name,
                        $item,
                        $domain->$item
                    );
                } elseif ($this->FieldsAdminOnly[$item]['type'] == 'boolean') {
                    if ($domain->$item == 1) {
                        $checked = "checked";
                    } else {
                        $checked = "";
                    }

                    printf(
                        "
                        <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input type=checkbox name=%s_form %s value=1></td>
                        </tr>",
                        $item_name,
                        $item,
                        $checked
                    );
                } else {
                    printf(
                        "
                        <tr>
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
                $item_name = $this->Fields[$item]['name'];
            } else {
                $item_name = preg_replace("/_/", " ", ucfirst($item));
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf(
                    "
                    <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                    </tr>
                    ",
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
                    "
                    <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input type=checkbox name=%s_form %s value=1></td>
                    </tr>
                    ",
                    $item_name,
                    $item,
                    $checked
                );
            } else {
                printf(
                    "
                    <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                    </tr>
                    ",
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

        $domain_old = $domain;

        foreach (array_keys($this->Fields) as $item) {
            $var_name = $item.'_form';
            //printf("<br>%s=%s", $var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $domain->$item = intval($_REQUEST[$var_name] == 1);
            } elseif ($this->Fields[$item]['type'] == 'boolean') {
                $domain->$item = intval($_REQUEST[$var_name]);
            } else {
                $domain->$item = trim($_REQUEST[$var_name]);
            }
        }

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                $var_name = $item.'_form';
                //printf("<br>%s=%s", $var_name,$_REQUEST[$var_name]);
                if ($this->FieldsAdminOnly[$item]['type'] == 'integer') {
                    $domain->$item = intval($_REQUEST[$var_name]);
                } elseif ($this->Fields[$item]['type'] == 'boolean') {
                    $domain->$item = intval($_REQUEST[$var_name]);
                } else {
                    $domain->$item = trim($_REQUEST[$var_name]);
                }
            }
        }

        $function = array(
            'commit'   => array(
                'name'       => 'updateDomain',
                'parameters' => array($domain),
                'logs'       => array('success' => sprintf('Domain %s has been updated', $domain->domain))
            )
        );

        return $this->SoapEngine->execute($function, $this->html);
    }

    function hide_html()
    {
        if ($_REQUEST['action'] == 'Export') {
            return true;
        } else {
            return false;
        }
    }

    function exportDomain($domain)
    {
        $exported_data= array();
        // Filter
        $filter = array(
                      'domain'    => $domain,
                      'customer'  => intval($this->filters['customer']),
                      'reseller'  => intval($this->filters['reseller'])
                      );

        // Range
        $range = array('start' => 0,
                     'count' => 1000
                     );
        // Compose query
        $Query = array('filter'  => $filter,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getDomains');
        $result     = $this->SoapEngine->soapclient->getDomains($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $i = 0 ;

            while ($i < $result->total) {
                $domain = $result->domains[$i];
                if (!in_array($domain->customer, $export_customers)) {
                    $export_customers[] = $domain->customer;
                }
                if (!in_array($domain->reseller, $export_customers)) {
                    $export_customers[] = $domain->reseller;
                }
                $i+=1;
                $exported_data['sip_domains'][] = objectToArray($domain);
            }
        }

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getAccounts');
        // Call function
        $result = call_user_func_array(array($this->SoapEngine->soapclient, 'getAccounts'), array($Query));

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $exported_data['sip_accounts'] = objectToArray($result->accounts);
            foreach ($result->accounts as $account) {
                if (!in_array($account->owner, $export_customers)) {
                    $export_customers[] = $account->owner;
                }

                $sipId = array("username" => $account->id->username,
                             "domain"   => $account->id->domain
                             );
                $this->SoapEngine->soapclientVoicemail->addHeader($this->SoapEngine->SoapAuthVoicemail);
                $result = $this->SoapEngine->soapclientVoicemail->getAccount($sipId);

                if ($this->soapHasError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode != "2000" && $error_fault->detail->exception->errorcode != "1010") {
                        printf("<p><font color=red>Error (VoicemailPort): %s (%s): %s</font>", $error_msg, $error_fault->detail->exception->errorcode, $error_fault->detail->exception->errorstring);
                    }
                } else {
                    $exported_data['voicemail_accounts'][] = $result;
                }

                // Filter
                $filter = array('targetUsername' => $account->id->username,
                              'targetDomain'   => $account->id->domain
                              );

                // Range
                $range = array('start' => 0,
                             'count' => 20
                             );

                // Compose query
                $Query = array('filter'  => $filter,
                             'range'   => $range
                             );

                // Call function
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAliases');
                $result = $this->SoapEngine->soapclient->getAliases($Query);

                if ($this->soapHasError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf(
                        "<p><font color=red>Error (SipPort): %s (%s): %s</font></p>",
                        $error_msg,
                        $error_fault->detail->exception->errorcode,
                        $error_fault->detail->exception->errorstring
                    );
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
            $filter = array(
                          'customer'     => intval($customer),
                          );

            // Compose query
            $Query = array('filter'     => $filter
                            );

            // Insert credetials
            $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuth);
            $this->log_action('getCustomers');

            // Call function
            $result     = $this->SoapEngine->soapclientCustomers->getCustomers($Query);
            if ($this->checkLogSoapError($result, true)) {
                return false;
            } else {
                $exported_data['customers'] = objectToArray($result->accounts);
            }
        }
        //print_r($exported_data['customers']);

        print_r(json_encode($exported_data));
    }
}
