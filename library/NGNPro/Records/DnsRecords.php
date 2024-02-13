<?php

class DnsRecords extends Records
{
    var $max_zones_selection = 50;
    var $typeFilter          = false;
    var $default_ttl         = 3600;
    var $fancy               = false;

    var $sortElements = array(
        'changeDate' => 'Change date',
        'type'       => 'Type',
        'name'       => 'Name'
    );

    var $FieldsReadOnly = array(
        'customer',
        'reseller'
    );

    var $Fields = array(
        'type'     => array('type'=>'string'),
        'priority' => array('type'=>'integer'),
        'value'    => array('type'=>'string'),
        'ttl'      => array('type'=>'integer')
    );

    var $recordTypes = array(
        'A'     => 'IP address',
        'AAAA'  => 'IP v6 address',
        'CNAME' => 'Hostname alias',
        'MX'    => 'Mail server address',
        'SRV'   => 'Server resource',
        'NS'    => 'Name server address',
        'NAPTR' => 'Name authority',
        'PTR'   => 'Reverse IP address',
        'TXT'   => 'Text',
        'LOC'   => 'Geo location'
    );

    var $havePriority         = array('MX','SRV','NAPTR');

    var $addRecordFunction    = 'addRecord';
    var $deleteRecordFunction = 'deleteRecord';
    var $updateRecordFunction = 'updateRecord';
    var $getRecordsFunction   = 'getRecords';
    var $getRecordFunction    = 'getRecord';

    var $recordTypesTemplate = array(
        'sip2sip' =>  array(
            'name'    => 'SIP2SIP infrastructure',
            'records' =>  array(
                'naptr1' => array(
                    'name'     => '',
                    'type'     => 'NAPTR',
                    'priority' => '100',
                    'ttl'      => '600',
                    'value'    => '20 100 "s" "SIP+D2T" "" _sip._tcp'
                ),
                'naptr2' => array(
                    'name'     => '',
                    'type'     => 'NAPTR',
                    'priority' => '100',
                    'ttl'      => '600',
                    'value'    => '30 100 "s" "SIP+D2U" "" _sip._udp'
                ),
                'naptr3' => array(
                    'name'     => '',
                    'type'     => 'NAPTR',
                    'priority' => '100',
                    'ttl'      => '600',
                    'value'    => '10 100 "s" "SIPS+D2T" "" _sips._tcp'
                ),
                'srv1'   => array(
                    'name'     => '_sip._tcp',
                    'type'     => 'SRV',
                    'priority' => '100',
                    'ttl'      => '600',
                    'value'    => '100 5060 proxy.sipthor.net'
                ),
                'srv2'   => array(
                    'name'     => '_sip._udp',
                    'type'     => 'SRV',
                    'priority' => '100',
                    'ttl'      => '600',
                    'value'    => '100 5060 proxy.sipthor.net'
                ),
                'srv3'   => array(
                    'name'     => '_sips._tcp',
                    'type'     => 'SRV',
                    'priority' => '100',
                    'ttl'      => '600',
                    'value'    => '100 5061 proxy.sipthor.net'
                ),
                'srv4'   => array(
                    'name'     => '_stun._udp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 3478 stun1.sipthor.net'
                ),
                'srv5'   => array(
                    'name'     => '_stun._udp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 3478 stun2.sipthor.net'
                ),
                'srv6'   => array(
                    'name'     => '_msrps._tcp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '0 2855 msrprelay.sipthor.net'
                ),
                'txt1'   => array(
                    'name'     => 'xcap',
                    'type'     => 'TXT',
                    'priority' => '10',
                    'value'    => 'https://xcap.sipthor.net/xcap-root'
                )
            ),
        ),
        'siptcp' =>  array(
            'name'    => 'SIP - TCP transport',
            'records' =>  array(
                'naptr' => array(
                    'name'     => '',
                    'type'     => 'NAPTR',
                    'priority' => '100',
                    'ttl'      => '3600',
                    'value'    => '10 100 "s" "SIP+D2T" "" _sip._tcp'
                ),
                'srv'   => array(
                    'name'     => '_sip._tcp',
                    'type'     => 'SRV',
                    'priority' => '100',
                    'ttl'      => '3600',
                    'value'    => '100 5060 #VALUE#|10 5060 sip'
                )
            ),
        ),
        'siptls' =>  array(
            'name'    => 'SIP - TLS transport',
            'records' =>  array(
                'naptr' => array(
                    'name'     => '',
                    'type'     => 'NAPTR',
                    'priority' => '100',
                    'ttl'      => '3600',
                    'value'    => '20 100 "s" "SIPS+D2T" "" _sips._tcp'
                ),
                'srv'   => array(
                    'name'     => '_sips._tcp',
                    'type'     => 'SRV',
                    'priority' => '100',
                    'ttl'      => '3600',
                    'value'    => '100 5061 #VALUE#|10 5061 sip'
                )
            )
        ),
        'sipudp' =>  array(
            'name'    => 'SIP - UDP transport',
            'records' =>  array(
                'naptr' => array(
                    'name'     => '',
                    'type'     => 'NAPTR',
                    'priority' => '100',
                    'ttl'      => '3600',
                    'value'    => '30 100 "s" "SIP+D2U" "" _sip._udp'
                ),
                'srv'   => array(
                    'name'     => '_sip._udp',
                    'type'     => 'SRV',
                    'priority' => '100',
                    'ttl'      => '3600',
                    'value'    => '100 5060 #VALUE#|10 5060 sip'
                )
            ),
        ),
        'stun' =>  array(
            'name'    => 'STUN - NAT mirror',
            'records' =>  array(
                'srv'   => array(
                    'name'     => '_stun._udp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 3478 #VALUE#|10 3478 stun'
                )
            ),
        ),
        'xmpp-server' =>  array(
            'name'    => 'XMPP server-to-server over TCP',
            'records' =>  array(
                'srv'   => array(
                    'name'     => '_xmpp-server._tcp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 5269 #VALUE#|10 5269 xmpp'
                ),
                'srv1'   => array(
                    'name'     => '_jabber._tcp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 5269 #VALUE#|10 5269 xmpp'
                )
            ),
        ),
        'xmpp-client' =>  array(
            'name'    => 'XMPP client-to-server over TCP',
            'records' =>  array(
                'srv'   => array(
                    'name'     => '_xmpp-client._tcp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 5222 #VALUE#|10 5222 xmpp'
                )
            ),
        ),
        'xmpps-server' =>  array(
            'name'    => 'XMPP server-to-server over TLS',
            'records' =>  array(
                'srv'   => array(
                    'name'     => '_xmpps-server._tcp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 5269 #VALUE#|10 5269 xmpp'
                ),
                'srv1'   => array(
                    'name'     => '_jabbers._tcp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 5269 #VALUE#|10 5269 xmpp'
                )
            ),
        ),
        'xmpps-client' =>  array(
            'name'    => 'XMPP client-to-server over TLS',
            'records' =>  array(
                'srv'   => array(
                    'name'     => '_xmpps-client._tcp',
                    'type'     => 'SRV',
                    'priority' => '0',
                    'value'    => '10 5222 #VALUE#|10 5222 xmpp'
                )
            ),
        ),
        'msrp' =>  array(
            'name'    => 'MSRP - IM relay',
            'records' =>  array(
                'srv'   => array(
                    'name'     => '_msrps._tcp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '0 2855 msrprelay'
                )
            )
        ),
        'sipthor' =>  array(
            'name'    => 'SIP - Thor network',
            'records' => array(
                'eventserver' => array(
                    'name'     => '_eventserver._tcp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '0 8000 eventserver'
                ),
                'sipserver' => array(
                    'name'     => '_sip._udp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '30 5060 proxy'
                ),
                'sipns1' => array(
                    'name'     => 'proxy',
                    'type'     => 'NS',
                    'value'    => 'ns1'
                ),
                'sipns2' => array(
                    'name'     => 'proxy',
                    'type'     => 'NS',
                    'value'    => 'ns2'
                ),
                'sipns3' => array(
                    'name'     => 'proxy',
                    'type'     => 'NS',
                    'value'    => 'ns3'
                ),
                'ngnproserver' => array(
                    'name'     => '_ngnpro._tcp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '0 9200 ngnpro'
                ),
                'ngnns1' => array(
                    'name'     => 'ngnpro',
                    'type'     => 'NS',
                    'value'    => 'ns1'
                ),
                'ngnns2' => array(
                    'name'     => 'ngnpro',
                    'type'     => 'NS',
                    'value'    => 'ns2'
                ),
                'ngnns3' => array(
                    'name'     => 'ngnpro',
                    'type'     => 'NS',
                    'value'    => 'ns3'
                ),
                'xcapserver' => array(
                    'name'     => '_xcap._tcp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '0 443 xcap'
                ),
                'xcapns1' => array(
                    'name'     => 'xcap',
                    'type'     => 'NS',
                    'value'    => 'ns1'
                ),
                'xcapns2' => array(
                    'name'     => 'xcap',
                    'type'     => 'NS',
                    'value'    => 'ns2'
                ),
                'xcapns3' => array(
                    'name'     => 'xcap',
                    'type'     => 'NS',
                    'value'    => 'ns3'
                ),
                'msrpserver' => array(
                    'name'     => '_msrps._tcp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '0 2855 msrprelay'
                ),
                'msrpns1' => array(
                    'name'     => 'msrprelay',
                    'type'     => 'NS',
                    'value'    => 'ns1'
                ),
                'msrpns2' => array(
                    'name'     => 'msrprelay',
                    'type'     => 'NS',
                    'value'    => 'ns2'
                ),
                'msrpns3' => array(
                    'name'     => 'msrprelay',
                    'type'     => 'NS',
                    'value'    => 'ns3'
                ),
                'voicemail' => array(
                    'name'     => '_voicemail._tcp',
                    'type'     => 'SRV',
                    'priority' => '10',
                    'value'    => '0 9200 voicemail'
                ),
                'vmns1' => array(
                    'name'     => 'voicemail',
                    'type'     => 'NS',
                    'value'    => 'ns1'
                ),
                'vmns2' => array(
                    'name'     => 'voicemail',
                    'type'     => 'NS',
                    'value'    => 'ns2'
                ),
                'vmns3' => array(
                    'name'     => 'voicemail',
                    'type'     => 'NS',
                    'value'    => 'ns3'
                )
            )
        )
    );


    public function __construct($SoapEngine)
    {
        dprint("init DnsRecords");

        $_name = trim($_REQUEST['name_filter']);

        if (strlen($_name) && !strstr($_name, '.') && !strstr($_name, '%')) {
            $_name .= '%';
        }

        if ($this->typeFilter) {
            $this->filters   = array(
                'id'           => trim($_REQUEST['id_filter']),
                'zone'         => trim($_REQUEST['zone_filter']),
                'name'         => $_name,
                'type'         => $this->typeFilter,
                'value'        => trim($_REQUEST['value_filter']),
                'owner'        => trim($_REQUEST['owner_filter'])
            );
        } else {
            $this->filters   = array(
                'id'           => trim($_REQUEST['id_filter']),
                'zone'         => trim($_REQUEST['zone_filter']),
                'name'         => $_name,
                'type'         => trim($_REQUEST['type_filter']),
                'value'        => trim($_REQUEST['value_filter']),
                'owner'        => trim($_REQUEST['owner_filter'])
            );
        }

        parent::__construct($SoapEngine);
        $this->getAllowedDomains();
    }

    function listRecords()
    {
        $this->showSeachForm();

        if ($this->typeFilter) {
            $filter = array(
                'id'       => intval($this->filters['id']),
                'zone'     => $this->filters['zone'],
                'name'     => $this->filters['name'],
                'type'     => $this->typeFilter,
                'value'    => $this->filters['value'],
                'owner'    => intval($this->filters['owner']),
                'customer' => intval($this->filters['customer']),
                'reseller' => intval($this->filters['reseller'])
            );
        } else {
            $filter = array(
                'id'       => intval($this->filters['id']),
                'zone'     => $this->filters['zone'],
                'name'     => $this->filters['name'],
                'type'     => $this->filters['type'],
                'value'    => $this->filters['value'],
                'owner'    => intval($this->filters['owner']),
                'customer' => intval($this->filters['customer']),
                'reseller' => intval($this->filters['reseller'])
            );
        }
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

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action($this->getRecordsFunction);

        // Call function
        $result = call_user_func_array(array($this->SoapEngine->soapclient, $this->getRecordsFunction), array($Query));

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $this->rows = $result->total;

            if ($this->rows > 1 && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print <<< END
<div class="alert alert-success">
    <center>$this->rows records found. Click on record id to edit the values.</center>
</div>
<p>
<table class='table table-striped table-condensed' width=100%>
END;
            if ($this->fancy) {
                print <<< END
    <thead>
    <tr>
        <th></th>
        <th>Zone owner</th>
        <th>Zone</th>
        <th>Id</th>
        <th>Name</th>
        <th>Type</th>
        <th>Value</th>
        <th>Owner</th>
        <th>Change date</th>
        <th>Actions</th>
    </tr>
    </thead>
END;
            } else {
                print <<< END
    <thead>
    <tr>
        <th></th>
        <th><b>Zone owner</b></th>
        <th><b>Zone</b></th>
        <th><b>Id</b></th>
        <th><b>Name</b></th>
        <th><b>Type</b></th>
        <th align=right><b>Priority</b></th>
        <th><b>Value</b></th>
        <th><b>TTL</b></th>
        <th><b>Change date</b></th>
        <th><b>Actions</b></th>
    </tr>
    </thead>
END;
            }

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
                    if (!$result->records[$i]) {
                        break;
                    }

                    $record = $result->records[$i];
                    $index = $this->next+$i+1;

                    $base_url_data = array(
                        'service' => $this->SoapEngine->service,
                        'zone_filter' => $record->zone,
                        'id_filter' => $record->id
                    );

                    $delete_url_data = array_merge(
                        $base_url_data,
                        array(
                            'action' => 'Delete',
                            'name_filter' => $record->name
                        )
                    );

                    $record_url_data = array_merge(
                        $base_url_data,
                        array(
                            'service' => sprintf(
                                '%s@%s',
                                $this->SoapEngine->service,
                                $this->SoapEngine->soapEngine,
                            )
                        )
                    );

                    $zone_url_data = array(
                        'service' => sprintf('dns_zones@%s', $this->SoapEngine->soapEngine),
                        'zone_filter' => $record->zone
                    );

                    $customer_url_data = array(
                        'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                        'customer_filter' => $record->customer
                    );

                    if ($this->adminonly) {
                        $delete_url_data['reseller_filter'] = $record->reseller;
                        $zone_url_data['reseller_filter'] = $record->reseller;
                        $record_url_data['reseller_filter'] = $record->reseller;
                    }

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $record->id) {
                        $delete_url_data['confirm'] = 1;
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->buildUrl($delete_url_data);
                    $_zone_url = $this->buildUrl($zone_url_data);
                    $_record_url = $this->buildUrl($record_url_data);
                    $_customer_url = $this->buildUrl($customer_url_data);

                    if ($this->fancy) {
                        printf(
                            "
                            <tr>
                                <td>%s</td>
                                <td><a href=%s>%s.%s</a></td>
                                <td><a href=%s>%s</a></td>
                                <td><a href=%s>%s</a></td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                            </tr>
                            ",
                            $index,
                            $_customer_url,
                            $record->customer,
                            $record->reseller,
                            $_zone_url,
                            $record->zone,
                            $_record_url,
                            $record->id,
                            $record->name,
                            $record->type,
                            $record->value,
                            $record->owner,
                            $record->changeDate,
                            $_url,
                            $actionText
                        );
                    } else {
                        printf(
                            "
                            <tr>
                                <td>%s</td>
                                <td><a href=%s>%s.%s</a></td>
                                <td><a href=%s>%s</a></td>
                                <td><a href=%s>%s</a></td>
                                <td>%s</td>
                                <td>%s</td>
                                <td align=right>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                            </tr>
                            ",
                            $index,
                            $_customer_url,
                            $record->customer,
                            $record->reseller,
                            $_zone_url,
                            $record->zone,
                            $_record_url,
                            $record->id,
                            $record->name,
                            $record->type,
                            $record->priority,
                            $record->value,
                            $record->ttl,
                            $record->changeDate,
                            $_url,
                            $actionText
                        );
                    }
                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($record);
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
            <div class='input-prepend'><span class='add-on'>Record Id</span><input class=span1 type=text size=7 name=id_filter value='%s'></div>
            ",
            $this->filters['id']
        );
        printf(
            "
            <div class='input-prepend'><span class='add-on'>Name</span><input class=span2 type=text size=20 name=name_filter value='%s'></div>
            ",
            $this->filters['name']
        );

        if (count($this->allowedDomains) > 0) {
            $selected_zone[$this->filters['zone']]='selected';
            print "<select class=span2 name=zone_filter><option value=''>Zone";
            foreach ($this->allowedDomains as $_zone) {
                printf(
                    "<option value='%s' %s>%s",
                    $_zone,
                    $selected_zone[$_zone],
                    $_zone
                );
            }
            print "</select>";
        } else {
            printf(
                "
                <div class='input-prepend'><span class='add-on'>DNS zone</span><input class=span2 type=text name=zone_filter value='%s'></div>
                ",
                $this->filters['zone']
            );
        }

        if ($this->typeFilter) {
            printf(
                "<input type=hidden name=%s_filter> Type %s",
                $this->typeFilter,
                $this->typeFilter
            );
        } else {
            $selected_type[$this->filters['type']]='selected';
            echo "
                <select name=type_filter class=span1><option value=''>Type";
            foreach (array_keys($this->recordTypes) as $_type) {
                printf(
                    "<option value='%s' %s>%s",
                    $_type,
                    $selected_type[$_type],
                    $_type
                );
            }
            echo "</select>";
        }
        printf(
            "
            <div class='input-prepend'><span class='add-on'>Value</span><input class=span2 type=text size=35 name=value_filter value='%s'></div>
            ",
            $this->filters['value']
        );
    }

    function deleteRecord($dictionary = array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['id']) {
            $id = $dictionary['id'];
        } else {
            $id = $this->filters['id'];
        }

        if (!$id) {
            print "<p><font color=red>Missing record id. </font>";
            return false;
        }

        $function = array(
            'commit'   => array(
                'name'       => $this->deleteRecordFunction,
                'parameters' => array($id),
                'logs'       => array('success' => sprintf('DNS record %s has been deleted', $id))
            )
        );

        $zone = $this->filters['zone'];
        unset($this->filters);
        $this->filters['zone'] = $zone;

        $result = $this->SoapEngine->execute($function, $this->html);

        return (bool)$result;
    }

    function showAddForm()
    {
        /*
        if ($this->adminonly) {
            if (!$this->filters['reseller']) {
                print "<p>To add a new record you must search first for a customer";
                return;
            }
        }
        */

        printf("<form class=form-inline method=post name=addform action=%s>", $_SERVER['PHP_SELF']);

        print "<div class='well well-small'>";

        if ($this->adminonly) {
            printf(
                "<input type=hidden name=reseller_filter value='%s'>",
                $this->filters['reseller']
            );
        }

        print "
            <input class='btn btn-warning' type=submit name=action value=Add>
                <div class='input-prepend'><span class='add-on'>Name</span>
        ";

        printf(
            "
            <input type=text class=span2 size=20 name=name value='%s'></div>
            ",
            trim($_REQUEST['name'])
        );

        if (count($this->allowedDomains) > 0) {
            if ($_REQUEST['zone']) {
                $selected_zone[$_REQUEST['zone']]='selected';
            } elseif ($this->filters['zone']) {
                $selected_zone[$this->filters['zone']]='selected';
            } elseif ($_zone = $this->getCustomerProperty('dns_records_last_zone')) {
                $selected_zone[$_zone]='selected';
            }

            print ".<select name=zone>";
            foreach ($this->allowedDomains as $_zone) {
                printf(
                    "<option value='%s' %s>%s",
                    $_zone,
                    $selected_zone[$_zone],
                    $_zone
                );
            }
            print "</select>";
        } else {
            if ($_REQUEST['zone']) {
                $_zone_selected = $_REQUEST['zone'];
            } elseif ($this->filters['zone']) {
                $_zone_selected = $this->filters['zone'];
            } elseif ($_zone = $this->getCustomerProperty('dns_records_last_zone')) {
                $_zone_selected = $_zone;
            }
            printf(
                "
                <div class='input-prepend'>
                <span class='add-on'>DNS zone</span>
                <input class=span2 type=text size=20 name=zone value='%s'>
                </div>
                ",
                $_zone_selected
            );
        }

        if ($this->typeFilter) {
            printf("Type %s <input type=hidden name=%s>", $this->typeFilter, $this->typeFilter);
        } else {
            print "<div class='input-prepend'><span class='add-on'>Type</span><select name=type>";

            if ($_REQUEST['type']) {
                $selected_type[$_REQUEST['type']]='selected';
            } elseif ($_type = $this->getCustomerProperty('dns_records_last_type')) {
                $selected_type[$_type]='selected';
            }

            foreach (array_keys($this->recordTypes) as $_type) {
                printf(
                    "<option value='%s' %s>%s - %s",
                    $_type,
                    $selected_type[$_type],
                    $_type,
                    $this->recordTypes[$_type]
                );
            }

            foreach (array_keys($this->recordTypesTemplate) as $_type) {
                printf(
                    "<option value='%s' %s>%s",
                    $_type,
                    $selected_type[$_type],
                    $this->recordTypesTemplate[$_type]['name']
                );
            }

            print "
            </select></div>
            ";
        }

        printf(
            "
            <div class='input-prepend'>
            <span class='add-on'>Value</span>
            <input class=span2 type=text size=35 name=value value='%s'>
            </div>
            ",
            trim($_REQUEST['value'])
        );

        if (!$this->fancy) {
            printf(
                "
                <div class='input-prepend'>
                <span class='add-on'>Priority</span>
                <input class=span1 type=text size=5 name=priority value='%s'>
                </div>
                ",
                trim($_REQUEST['priority'])
            );
        }

        $this->printHiddenFormElements();

        print "</div>
        </form>
        ";
    }

    function getAllowedDomains()
    {
        // Filter
        $filter = array(
            'customer' => intval($this->filters['customer']),
            'reseller' => intval($this->filters['reseller'])
        );
        // Range
        $range = array(
            'start' => 0,
            'count' => $this->max_zones_selection
        );

        // Order
        $orderBy = array(
            'attribute' => 'name',
            'direction' => 'ASC'
        );

        // Compose query
        $Query = array(
            'filter'  => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getZones');
        $result = $this->SoapEngine->soapclient->getZones($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if ($result->total  > $this->max_zones_selection) return false;
            foreach ($result->zones as $zone) {
                if (in_array($zone->name, $this->allowedDomains)) continue;
                $this->allowedDomains[] = $zone->name;
                $seen[$zone->name]++;
            }
        }
    }

    function addRecord($dictionary = array())
    {
        if ($this->typeFilter) {
            $type = $this->typeFilter;
        } elseif ($dictionary['type']) {
            $type = $dictionary['type'];
        } else {
            $type = trim($_REQUEST['type']);
        }

        if ($dictionary['name']) {
            $name = $dictionary['name'];
        } else {
            $name = trim($_REQUEST['name']);
        }

        $name = rtrim($name, ".");

        if (preg_match("/^(.+)@(.*)$/", $name, $m)) {
            $zone = $m[2];
        } else {
            if ($dictionary['zone']) {
                $zone = $dictionary['zone'];
                $this->skipSaveProperties = true;
            } elseif ($_REQUEST['zone']) {
                $zone = $_REQUEST['zone'];
            }

            if ($type == 'MBOXFW') {
                $name .= '@'.$zone;
            }
        }

        if (!strlen($zone)) {
            if ($this->html) {
                echo "<div class='alert alert-danger'><strong>Error</strong>: Missing zone name. </div>";
            }
            return false;
        }

        $this->filters['zone'] = $zone;

        if (!strlen($type)) {
            if ($this->html) {
                echo "<div class='alert alert-danger'><strong>Error</strong>: Missing record type.</div>";
            }
            return false;
        }

        if ($dictionary['value']) {
            $value = $dictionary['value'];
        } else {
            $value = trim($_REQUEST['value']);
        }

        $value = rtrim($value, ".");

        if ($this->adminonly) {
            if ($dictionary['reseller']) {
            } elseif ($this->filters['reseller']) {
            } else {
                if ($this->html) {
                    print <<< END
<div class="alert alert-danger">
    <strong>Error</strong>: Missing reseller, please first search zones for a given reseller
</div>
END;
                }
                return false;
            }
        }

        if ($dictionary['ttl']) {
            $ttl = intval($dictionary['ttl']);
        } else {
            $ttl = intval(trim($_REQUEST['ttl']));
        }

        if (!$ttl) $ttl=3600;

        if ($dictionary['owner']) {
            $owner = intval($dictionary['owner']);
        } else {
            $owner = intval(trim($_REQUEST['owner']));
        }

        if ($dictionary['priority']) {
            $priority = $dictionary['priority'];
        } else {
            $priority = trim($_REQUEST['priority']);
        }

        if (in_array($type, array_keys($this->recordTypes))) {
            // See RFC 1912 - Section 2.4
            if (trim($name).trim($zone) == trim($zone) && $type == 'CNAME') {
                printf(
                    "
                    <div class='alert alert-danger'>
                    <strong>Error</strong>: CNAME (%s) equal to zone name (%s) is not allowed
                    </div>
                    ",
                    trim($name).trim($zone),
                    trim($zone)
                );
                return false;
            }

            if (!strlen($value)) {
                if ($this->html) {
                    echo "<div class='alert alert-danger'><strong>Error</strong>: Missing record value.</div>";
                }
                return false;
            }

            $record = array(
                'name'     => trim($name),
                'zone'     => trim($zone),
                'type'     => $type,
                'value'    => trim($value),
                'owner'    => intval($owner),
                'ttl'      => intval($ttl),
                'priority' => intval($priority)
            );

            if (!$this->skipSaveProperties = true) {
                $_p = array(
                    array(
                        'name'       => 'dns_records_last_zone',
                        'category'   => 'web',
                        'value'      => $_REQUEST['zone'],
                        'permission' => 'customer'
                    ),
                    array(
                        'name'       => 'dns_records_last_type',
                        'category'   => 'web',
                        'value'      => "$type",
                        'permission' => 'customer'
                    )
                );

                $this->setCustomerProperties($_p);
            }

            $function = array(
                'commit'   => array(
                    'name'       => $this->addRecordFunction,
                    'parameters' => array($record),
                    'logs'       => array('success' => sprintf('DNS record %s under %s has been added', $name, $zone))
                )
            );

            $result = $this->SoapEngine->execute($function, $this->html);
            dprint_r($result);

            return (bool)$result;
        } elseif (in_array($type, array_keys($this->recordTypesTemplate))) {
            $push_notifications_server = $this->getResellerProperty('push_notifications_server_private')
                or $this->getResellerProperty('push_notifications_server');
            if ($type == "sip2sip" && $push_notifications_server) {
                if (preg_match("/^(.*):(\d+);transport=(.*)$/", $push_notifications_server, $m)) {
                    $push_hostname = $m[1];
                    $push_port = $m[2];
                    $push_transport = $m[3];
                    if ($push_transport == "tls") {
                        $naptr_type = "_sips._tcp";
                        $naptr_s = "SIPS+D2T";
                    } elseif ($push_transport == "tcp") {
                        $naptr_type = "_sip._tcp";
                        $naptr_s = "SIP+D2T";
                    } else {
                        $naptr_type = "_sip._udp";
                        $naptr_s = "SIP+D2U";
                    }

                    $this->recordTypesTemplate[$type]['records']['push_naptr'] =
                                        array(
                                            'name'     => 'push',
                                            'type'     => 'NAPTR',
                                            'priority' => '100',
                                            'ttl'      => '600',
                                            'value'    => sprintf('10 100 "s" "%s" "" %s.push', $naptr_s, $naptr_type)
                                        );
                    $this->recordTypesTemplate[$type]['records']['push_srv'] =
                                        array(
                                            'name'     => sprintf('%s.push', $naptr_type),
                                            'type'     => 'SRV',
                                            'priority' => '100',
                                            'ttl'      => '600',
                                            'value'    => sprintf('100 %d %s', $push_port, $push_hostname)
                                        );
                }
            }

            foreach (array_values($this->recordTypesTemplate[$type]['records']) as $_records) {
                $value_new='';

                if (strlen($_records['value'])) {
                    if (preg_match("/^_sip/", $_records['name'])) {
                        if (!$value) {
                            $value = $this->getCustomerProperty('dns_records_last_sip_server');
                            if (!$value) {
                                $value = $this->getCustomerProperty('sip_proxy');
                            }
                            if (!value) {
                                $value = $this->SoapEngine->default_sip_proxy;
                            }
                            $save_new_value = false;
                        } else {
                            $save_new_value = true;
                        }
                    }

                    $els = explode("|", $_records['value']);

                    foreach ($els as $el) {
                        if (preg_match("/#VALUE#/", $el)) {
                            if ($value) {
                                $value_new = preg_replace("/#VALUE#/", $value, $el);
                            } else {
                                continue;
                            }
                        } else {
                            $value_new = $el;
                        }
                        break;
                    }

                    // save value if type sip server
                    if ($save_new_value && $_records['name'] && preg_match("/^_sip/", $_records['name'])) {
                        $_p = array(
                            array(
                                'name'       => 'dns_records_last_sip_server',
                                'category'   => 'web',
                                'value'      => $value,
                                'permission' => 'customer'
                            )
                        );

                        $this->setCustomerProperties($_p);
                    }
                }

                if (!in_array($_records['type'], array_keys($this->recordTypes))) {
                    continue;
                }

                $record = array(
                    'name'     => $_records['name'],
                    'zone'     => trim($zone),
                    'type'     => $_records['type'],
                    'value'    => $value_new,
                    'owner'    => intval($owner),
                    'ttl'      => intval($_records['ttl']),
                    'priority' => intval($_records['priority'])
                );

                //print_r($record);
                $function = array(
                    'commit'   => array(
                        'name'       => $this->addRecordFunction,
                        'parameters' => array($record),
                        'logs'       => array(
                            'success' => sprintf('Dns %s record under %s has been added', $_records['type'], $zone)
                        )
                    )
                );

                $result = $this->SoapEngine->execute($function, $this->html);

                return (bool)$result;
            }
        } else {
            if ($this->html) {
                print "<div class='alert alert-danger'><strong>Error</strong>: Invalid or missing record type.</div>";
            }
            return false;
        }
        return true;
    }

    function getRecordKeys()
    {
        // Filter
        $filter = array(
            'id'       => intval($this->filters['id']),
            'zone'     => $this->filters['zone'],
            'name'     => $this->filters['name'],
            'type'     => $this->filters['type'],
            'value'    => $this->filters['value'],
            'owner'    => intval($this->filters['owner']),
            'customer' => intval($this->filters['customer']),
            'reseller' => intval($this->filters['reseller'])
        );

        // Range
        $range = array(
            'start' => 0,
            'count' => 1000
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
        $this->log_action('getRecords');

        // Call function
        $result = $this->SoapEngine->soapclient->getRecords($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            foreach ($result->records as $record) {
                $this->selectionKeys[] = array('id' => $record->id);
            }
            return true;
        }
    }

    function showRecord($record)
    {
        echo "<h3>Record</h3>";
        printf("<form class='form-horizontal' method=post name=addform action=%s>", $_SERVER['PHP_SELF']);
        echo "<input type=hidden name=action value=Update>";

        printf(
            "
            <div class='control-group'>
                <label class='control-label'>Name</label>
                <div class='controls' style='padding-top: 5px'>%s</div>
            </div>
            ",
            $record->name
        );

        foreach (array_keys($this->Fields) as $item) {
            if (is_array($this->havePriority) && $item == 'priority' && !in_array($record->type, $this->havePriority)) {
                continue;
            }

            if ($this->Fields[$item]['name']) {
                $item_name = $this->Fields[$item]['name'];
            } else {
                $item_name = ucfirst($item);
            }

            if ($item == 'type') {
                $selected_type[$record->$item]='selected';

                $select_box=sprintf("<select name=%s_form>", $item);
                foreach (array_keys($this->recordTypes) as $_type) {
                    $select_box .= sprintf(
                        "<option value='%s' %s>%s - %s",
                        $_type,
                        $selected_type[$_type],
                        $_type,
                        $this->recordTypes[$_type]
                    );
                }

                foreach (array_keys($this->recordTypesTemplate) as $_type) {
                    $select_box .= sprintf(
                        "<option value='%s' %s>%s",
                        $_type,
                        $selected_type[$_type],
                        $this->recordTypesTemplate[$_type]['name']
                    );
                }

                $select_box .= "</select>";

                printf(
                    "
                    <div class='control-group'>
                        <label class='control-label'>%s</label>
                        <div class='controls'>%s</div>
                    </div>",
                    $item_name,
                    $select_box
                );
            } elseif ($this->Fields[$item]['type'] == 'text') {
                printf(
                    "
                    <div class='control-group'>
                        <label class='control-label'>%s</label>
                        <div class='controls'><textarea cols=0 name=%s_form rows=4>%s</textarea></div>
                    </div>",
                    $item_name,
                    $item,
                    $record->$item
                );
            } else {
                if ($record->type == 'NAPTR' and $item == 'value') {
                    $help_text = 'Priority field will be used for the <i>preference</i> part of the value';
                } else {
                    $help_text = '';
                }

                printf(
                    "
                    <div class='control-group'>
                        <label class='control-label'>%s</label>
                        <div class='controls'><input name=%s_form size=40 type=text value='%s'>
                    ",
                    $item_name,
                    $item,
                    $record->$item
                );
                if ($help_text) {
                    printf("<span class='help-inline'>%s</span>", $help_text);
                }
                echo "</div></div>";
            }
        }

        printf("<input type=hidden name=id_filter value='%s'>", $record->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        echo "
            <div class='form-actions'>
                <input type=submit value=Update class='btn btn-warning'>
            </div>
        ";
        echo "</form>";
    }

    function getRecord($id)
    {
        // Filter
        if (!$id) {
            print "Error in getRecord(): Missing record id";
            return false;
        }

        $filter = array('id'   => $id);

        // Range
        $range = array(
            'start' => 0,
            'count' => 1
        );

        // Order
        $orderBy = array(
            'attribute' => 'changeDate',
            'direction' => 'DESC'
        );

        // Compose query
        $Query = array(
            'filter'  => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action($this->getRecordsFunction);

        // Call function
        $result = call_user_func_array(array($this->SoapEngine->soapclient, $this->getRecordsFunction), array($Query));

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if ($result->records[0]) {
                return $result->records[0];
            } else {
                return false;
            }
        }
    }

    function updateRecord()
    {
        //print "<p>Updating record ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$record = $this->getRecord(intval($_REQUEST['id_filter']))) {
            return false;
        }

        $record_old = $record;

        foreach (array_keys($this->Fields) as $item) {
            $var_name = $item.'_form';
            //printf ("<br>%s=%s", $var_name, $_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $record->$item = intval($_REQUEST[$var_name]);
            } else {
                $record->$item = trim($_REQUEST[$var_name]);
            }
        }

        $function = array('commit'   => array('name'       => $this->updateRecordFunction,
                                            'parameters' => array($record),
                                            'logs'       => array('success' => sprintf('Record %s has been updated', $_REQUEST['id_filter'])))
                        );

        $result = $this->SoapEngine->execute($function, $this->html);
        dprint_r($result);

        return (bool)$result;
    }

    function showTextBeforeCustomerSelection()
    {
        print _("Zone owner");
    }
}
