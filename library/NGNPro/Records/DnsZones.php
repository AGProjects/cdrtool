<?php

class DnsZones extends Records
{
    var $FieldsAdminOnly = array(
        'reseller'      => array(
            'type'=>'integer',
            'help' => 'Zone owner'
        )
    );

    var $Fields = array(
        'customer' => array(
            'type' => 'integer',
            'help' => 'Zone owner'
        ),
        'serial' => array(
            'type' => 'integer',
            'help' => 'Serial number',
            'readonly' => 1
        ),
        'email' => array(
            'type' => 'string',
            'help' => 'Administrator address'
        ),
        'ttl' => array(
            'type' => 'integer',
            'help' => 'Time to live of SOA record'
        ),
        'minimum' => array(
            'type' => 'integer',
            'help' => 'Default time to live period'
        ),
        'retry' =>  array(
            'type' => 'integer',
            'help' => 'Retry transfer period'
        ),
        'expire' => array(
            'type' => 'integer',
            'help' => 'Expire period'
        ),
        'info' => array(
            'type' => 'string',
            'help' => 'Zone description'
        )
    );

    public function __construct($SoapEngine)
    {
        dprint("init DnsZones");

        $this->filters = array(
            'name' => trim($_REQUEST['name_filter']),
            'info' => trim($_REQUEST['info_filter'])
        );

        parent::__construct($SoapEngine);

        $this->sortElements = array(
            'changeDate' => 'Change date',
            'name'     => 'Name'
                                 );
        $this->Fields['nameservers'] = array(
            'type'=>'text',
            'name'=>'Name servers',
            'help'=>'Authoritative name servers'
        );
    }

    function showAfterEngineSelection()
    {
        if ($this->SoapEngine->name_servers) {
        //printf(" Available name servers: %s", $this->SoapEngine->name_servers);
        }
    }

    function listRecords()
    {
        $this->showSeachForm();

        // Filter
        $filter = array(
            'name'     => $this->filters['name'],
            'info'     => $this->filters['info'],
            'customer' => intval($this->filters['customer']),
            'reseller' => intval($this->filters['reseller'])
        );

        // Range
        $range = array(
            'start' => intval($this->next),
            'count' => intval($this->maxrowsperpage)
        );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'DESC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query = array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getZones');
        $result = $this->SoapEngine->soapclient->getZones($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print <<< END
<div class="alert alert-success"><center>$this->rows records found</center></div>
    <p>
    <table class='table table-striped table-condensed' width=100%>
    <thead>
    <tr>
        <th>Id</th>
        <th>Owner</th>
        <th>Zone</th>
        <th>Administrator</th>
        <th>Info</th>
        <th></th>
        <th>Serial</th>
        <th>Default TTL</th>
        <th>Change date</th>
        <th>Actions</th>
    </tr>
    </thead>
END;

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
                    if (!$result->zones[$i]) break;
                    $zone = $result->zones[$i];

                    $base_url_data = array(
                        'service' => $this->SoapEngine->service,
                        'name_filter' => $zone->name
                    );

                    $delete_url_data = array_merge(
                        $base_url_data,
                        array(
                            'action' => 'Delete'
                        )
                    );
                    $zone_url_data = $base_url_data;
                    $records_url_data = array(
                        'service' => sprintf('dns_records@%s', $this->SoapEngine->soapEngine),
                        'zone_filter' => $zone->name
                    );

                    $customer_url_data = array(
                        'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                        'customer_filter' => $zone->customer
                    );

                    $index = $this->next + $i + 1;

                    if ($this->adminonly) {
                        $delete_url_data['reseller_filter'] = $zone->reseller;
                        $zone_url_data['reseller_filter'] = $zone->reseller;
                        $records_url_data['reseller_filter'] = $zone->reseller;
                    }

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['name_filter'] == $zone->name) {
                        $delete_url_data['confirm'] = 1;
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->buildUrl($delete_url_data);
                    $zone_url = $this->buildUrl($zone_url_data);
                    $records_url = $this->buildUrl($records_url_data);
                    $customer_url = $this->buildUrl($customer_url_data);

                    sort($zone->nameservers);

                    $ns_text = '';

                    foreach ($zone->nameservers as $ns) {
                        $ns_text.= $ns." ";
                    }

                    printf(
                        "
                        <tr>
                        <td>%s</td>
                        <td><a href=%s>%s.%s</a></td>
                        <td><a href=%s>%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>Records</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                        </tr>
                        ",
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
                    print "</tr>";

                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($zone);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function deleteRecord($dictionary = array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if (!strlen($this->filters['name'])) {
            print "<p><font color=red>Error: missing Dns zone name </font>";
            return false;
        }

        $name = $this->filters['name'];

        $function = array(
            'commit' => array(
                'name'       => 'deleteZone',
                'parameters' => array($name),
                'logs'       => array(
                    'success' => sprintf('Dns zone %s has been deleted', $this->filters['name'])
                )
            )
        );

        unset($this->filters);

        $result = $this->SoapEngine->execute($function, $this->html);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            return true;
        }
    }

    function showAddForm()
    {
        if ($this->selectionActive) return;

        printf(
            "<form class=form-inline method=post name=addform action=%s enctype='multipart/form-data'>",
            $_SERVER['PHP_SELF']
        );
        print "<div class='well well-small'>";

        print "<input class='btn btn-warning' type=submit name=action value=Add>";
        $this->showCustomerTextBox();

        printf(
            "
            </div>
            <div class='input-prepend'>
                <span class='add-on'>DNS zone</span>
                <input class=span2 type=text size=25 name=name value='%s'>
            </div>
            ",
            $_REQUEST['name']
        );

        $this->printHiddenFormElements();

        print <<< END
Import DNS zones from file:
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
END;

        print "
            </div>
        </form>
        ";
    }

    function addRecord($dictionary = array())
    {
        $name         = trim($_REQUEST['name']);
        $info         = trim($_REQUEST['info']);
        $name_servers = trim($_REQUEST['name_servers']);

        if ($_FILES['import_file']['tmp_name']) {
            $content = fread(fopen($_FILES['import_file']['tmp_name'], "r"), $_FILES['import_file']['size']);
            //print_r($content);

            if (!$imported_data = json_decode($content, true)) {
                printf("<p><font color=red>Error: reading imported data. </font>");
                return false;
            }

            //print_r($imported_data);

            if (!in_array('dns_zones', array_keys($imported_data))) {
                printf("<p><font color=red>Error: Missing zones in imported data. </font>");
                return false;
            }

            if (!in_array('dns_records', array_keys($imported_data))) {
                return false;
                printf("<p><font color=red>Error: Missing records in imported data. </font>");
            }

            foreach ($imported_data['customers'] as $customer) {
                // Insert credetials
                $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuth);

                $customer['credit'] = floatval($customer['credit']);
                $customer['balance'] = floatval($customer['balance']);
                // Call function
                $this->log_action('addAccount');

                $result     = $this->SoapEngine->soapclientCustomers->addAccount($customer);
                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 5001) {
                        $result     = $this->SoapEngine->soapclientCustomers->updateCustomer($customer);
                        if (!$this->checkLogSoapError($result, true, true)) {
                            printf('<p>Customer %s has been updated', $customer['id']);
                        }
                    } else {
                        $log = sprintf(
                            "SOAP request error from %s: %s (%s): %s</font>",
                            $this->SoapEngine->SOAPurl,
                            $error_msg,
                            $error_fault->detail->exception->errorcode,
                            $error_fault->detail->exception->errorstring
                        );
                        syslog(LOG_NOTICE, $log);
                        printf("<p><font color=red>Error: $log</font>");
                    }
                } else {
                    printf('<p>Customer %s has been added', $customer['id']);
                }
            }

            $name_servers = array();
            foreach ($imported_data['dns_zones'] as $zone) {
                flush();
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addZone');
                $result = $this->SoapEngine->soapclient->addZone($zone);
                $name_servers[$zone['name']] = $zone['nameservers'];
                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 7001) {
                        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                        $this->log_action('updateZone');
                        $result = $this->SoapEngine->soapclient->updateZone($zone);
                        if (!$this->checkLogSoapError($result, true, true)) {
                             printf('<p>Zone %s has been updated', $zone['name']);
                        }
                    } else {
                        $log = sprintf(
                            "SOAP request error from %s: %s (%s): %s</font>",
                            $this->SoapEngine->SOAPurl,
                            $error_msg,
                            $error_fault->detail->exception->errorcode,
                            $error_fault->detail->exception->errorstring
                        );
                        syslog(LOG_NOTICE, $log);
                        printf("<p><font color=red>Error: $log</font>");
                    }
                } else {
                    printf('<p>Zone %s has been added', $zone['name']);
                }
            }

            $added = 0;
            $updated = 0;
            foreach ($imported_data['dns_records'] as $record) {
                flush();
                if (in_array($record['name'], $name_servers[$record['zone']]) && $record['type'] == "A") {
                    continue;
                }
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('addRecord');
                $result = $this->SoapEngine->soapclient->addRecord($record);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    if ($error_fault->detail->exception->errorcode == 7003) {
                        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                        $this->log_action('updateRecord');
                        $result = $this->SoapEngine->soapclient->updateRecord($record);
                        if (!$this->checkLogSoapError($result, true, true)) {
                            $added += 1;
                        }
                    } else {
                        $log = sprintf(
                            "SOAP request error from %s: %s (%s): %s</font>",
                            $this->SoapEngine->SOAPurl,
                            $error_msg,
                            $error_fault->detail->exception->errorcode,
                            $error_fault->detail->exception->errorstring
                        );
                        syslog(LOG_NOTICE, $log);
                        printf("<p><font color=red>Error: $log</font>");
                    }
                } else {
                    $added += 1;
                }
            }

            printf('<p>%d DNS records added and %d updated', $added, $updated);

            return true;
        } else {
            if (isset($this->SoapEngine->allow_none_local_dns_zones)) {
                $allow_none_local_dns_zones = $this->SoapEngine->allow_none_local_dns_zones;
            } else {
                $allow_none_local_dns_zones = false;
            }

            if (!strlen($name)) {
                printf("<p class='alert alert-danger'><strong>Error</strong>: Missing zone name.</p>");
                return false;
            }
            $lookup1 = dns_get_record($name);
            //dprint_r($lookup1);

            $ns_array1=explode(" ", trim($this->SoapEngine->name_servers));

            if (empty($lookup1) || $allow_none_local_dns_zones) {
                $valid = 1;
            } else {
                $valid = 0;
                foreach ($lookup1 as $lrecord) {
                    if ($lrecord['type'] == 'NS') {
                        if (in_array($lrecord['target'], $ns_array1)) {
                            $valid = 1 ;
                        }
                    }
                }
            }

            if ($valid==0) {
                printf("<p class='alert alert-danger'><strong>Error</strong>: DNS zone already exists on other server. Please contact our support if you plan to transfer this DNS zone to this system. </p>");
                return false;
            }

            if (is_numeric($prefix)) {
                printf("<p><font color=red>Error: Numeric zone names are not allowed. Use ENUM port instead. </font>");
                return false;
            }

            list($customer, $reseller)=$this->customerFromLogin($dictionary);

            if (!trim($_REQUEST['ttl'])) {
                $ttl=3600;
            } else {
                $ttl = intval(trim($_REQUEST['ttl']));
            }

            if ($name_servers) {
                $ns_array = explode(" ", trim($name_servers));
            } elseif ($this->login_credentials['login_type'] != 'admin' && $this->SoapEngine->name_servers) {
                $ns_array = explode(" ", trim($this->SoapEngine->name_servers));
            } else {
                $ns_array = array();
            }

            $zone = array(
                         'name'        => $name,
                         'ttl'         => $ttl,
                         'info'        => $info,
                         'customer'    => intval($customer),
                         'reseller'    => intval($reseller),
                         'nameservers' => $ns_array
                        );

            $function = array('commit'   => array('name'       => 'addZone',
                                                'parameters' => array($zone),
                                                'logs'       => array('success' => sprintf('DNS zone %s has been added', $name)))
                            );

            $result = $this->SoapEngine->execute($function, $this->html);
            dprint_r($result);

            if ($this->checkLogSoapError($result, true)) {
                return false;
            } else {
                return true;
            }
        }
    }

    function showSeachFormCustom()
    {
        printf(
            "
            <div class='input-prepend'>
                <span class='add-on'>DNS zone</span>
                <input type=text class=span2 size=25 name=name_filter value='%s'>
            </div>
            ",
            $this->filters['name']
        );
        printf(
            "
            <div class='input-prepend'>
                <span class='add-on'>Info</span>
                <input class=span2 type=text size=25 name=info_filter value='%s'>
            </div>
            ",
            $this->filters['info']
        );
    }

    function showRecord($zone)
    {
        print <<< END
<table border=0 cellpadding=10>
    <tr>
        <td valign=top>
        <table border=0>
END;
        printf("<form method=post name=addform action=%s>", $_SERVER['PHP_SELF']);

        print <<< END
            <input type=hidden name=action value=Update>";
            <tr>
                <td colspan=2><input type=submit value=Update></td>
            </tr>
END;

        printf("<tr><td class=border>DNS zone</td><td class=border>%s</td></td>", $zone->name);

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                if ($item == 'nameservers') {
                    foreach ($zone->$item as $_item) {
                        $nameservers.=$_item."\n";
                    }
                    $item_value = $nameservers;
                } else {
                    $item_value = $zone->$item;
                }

                if ($this->FieldsAdminOnly[$item]['name']) {
                    $item_name = $this->FieldsAdminOnly[$item]['name'];
                } else {
                    $item_name = ucfirst($item);
                }

                if ($this->FieldsAdminOnly[$item]['type'] == 'text') {
                    printf(
                        "
                        <tr>
                            <td class=border valign=top>%s</td>
                            <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                            <td class=border valign=top>%s</td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $item_value,
                        $this->FieldsAdminOnly[$item]['help']
                    );
                } else {
                    printf(
                        "
                        <tr>
                            <td class=border valign=top>%s</td>
                            <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                            <td class=border>%s</td>
                        </tr>
                        ",
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
                $item_name = $this->Fields[$item]['name'];
            } else {
                $item_name = ucfirst($item);
            }

            if ($item == 'nameservers') {
                foreach ($zone->$item as $_item) {
                    $nameservers.=$_item."\n";
                }
                $item_value = $nameservers;
            } else {
                $item_value = $zone->$item;
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf(
                    "
                    <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                        <td class=border valign=top>%s</td>
                    </tr>
                    ",
                    $item_name,
                    $item,
                    $item_value,
                    $this->Fields[$item]['help']
                );
            } elseif ($this->Fields[$item]['readonly']) {
                printf(
                    "
                    <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border>%s</td>
                        <td class=border valign=top>%s</td>
                    </tr>
                    ",
                    $item_name,
                    $item_value,
                    $this->Fields[$item]['help']
                );
            } else {
                printf(
                    "
                    <tr>
                        <td class=border valign=top>%s</td>
                        <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                        <td class=border>%s</td>
                    </tr>
                    ",
                    $item_name,
                    $item,
                    $item_value,
                    $this->Fields[$item]['help']
                );
            }
        }

        printf("<input type=hidden name=tld_filter value='%s'>", $zone->id->tld);
        printf("<input type=hidden name=prefix_filter value='%s'>", $zone->id->prefix);
        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "</form>";
        print "
        </table>
        ";
    }

    function updateRecord()
    {
        if (!$_REQUEST['name_filter']) return;
        //dprintf("<p>Updating zone %s...", $_REQUEST['name_filter']);

        $filter = array('name' => $_REQUEST['name_filter']);

        if (!$zone = $this->getRecord($filter)) {
            return false;
        }

        $zone_old = $zone;

        foreach (array_keys($this->Fields) as $item) {
            $var_name = $item.'_form';
            //printf("<br>%s=%s", $var_name, $_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $zone->$item = intval($_REQUEST[$var_name]);
            } elseif ($item == 'nameservers') {
                $_txt = trim($_REQUEST[$var_name]);
                if (!strlen($_txt)) {
                    unset($zone->$item);
                } else {
                    $_nameservers = array();
                    $_lines = explode("\n", $_txt);
                    foreach ($_lines as $_line) {
                        $_ns = trim($_line);
                        $_nameservers[] = $_ns;
                    }
                    $zone->$item = $_nameservers;
                }
            } else {
                $zone->$item = trim($_REQUEST[$var_name]);
            }
        }

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                $var_name = $item.'_form';
                if ($this->FieldsAdminOnly[$item]['type'] == 'integer') {
                    $zone->$item = intval($_REQUEST[$var_name]);
                } else {
                    $zone->$item = trim($_REQUEST[$var_name]);
                }
            }
        }

        $function = array('commit'   => array('name'       => 'updateZone',
                                            'parameters' => array($zone),
                                            'logs'       => array('success' => sprintf('DNS zone %s has been updated', $filter['name'])))
                        );

        $result = $this->SoapEngine->execute($function, $this->html);

        dprint_r($result);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            return true;
        }
    }

    function getRecord($zone) {
        // Filter
        if (!$zone['name']) {
            print "Error in getRecord(): Missing zone name";
            return false;
        }

        $filter = array('name'   => $zone['name']);

        // Range
        $range = array('start' => 0,
                     'count' => 1
                     );

        // Order
        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
                         );

        // Compose query
        $Query = array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getZones');
        $result     = $this->SoapEngine->soapclient->getZones($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if ($result->zones[0]) {
                return $result->zones[0];
            } else {
                return false;
            }
        }
    }

    function getRecordKeys()
    {
        // Filter
        $filter = array(
            'name'     => $this->filters['name'],
            'info'     => $this->filters['info'],
            'customer' => intval($this->filters['customer']),
            'reseller' => intval($this->filters['reseller'])
        );

        // Range
        $range = array(
            'start' => 0,
            'count' => 200
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
        $this->log_action('getZones');

        // Call function
        $result     = $this->SoapEngine->soapclient->getZones($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            foreach ($result->zones as $zone) {
                $this->selectionKeys[] = array('name' => $zone->name);
            }
            return true;
        }
    }

    function hide_html()
    {
        if ($_REQUEST['action'] == 'PerformActions' && $_REQUEST['sub_action'] == 'export') {
            return true;
        } else {
            return false;
        }
    }
}
