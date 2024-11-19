<?php

class TrustedPeers extends Records
{
    var $FieldsAdminOnly = array(
        'msteams'    => array('type'=>'boolean', 'name' => 'MS Teams'),
        'prepaid'    => array('type'=>'boolean'),
        'blocked'    => array('type'=>'boolean'),
        'tenant'     => array('type'=>'string'),
        'carrierName' => array('type'=>'string', 'name' => 'LCR carrier'),
        'originator' => array('type'=>'string', 'name' => 'LCR originator'),
        'callLimit'  => array('type'=>'integer', 'name' => 'Capacity')
    );
    var $Fields = array(
        'description'  => array('type'=>'string'),
        'prefix'  => array('type'=>'string', 'name' => 'Add prefix'),
        'strip'  => array('type'=>'integer', 'name' => 'Strip digits'),
        'authToken'    => array('type'=>'string', 'name' => 'Authentication token')
    );

    public function __construct($SoapEngine)
    {
        $this->filters   = array(
            'ip'     => trim($_REQUEST['ip_filter']),
            'tenant'   => trim($_REQUEST['tenant_filter']),
            'description'  => trim($_REQUEST['description_filter']),
            'msteams'  => trim($_REQUEST['msteams_filter']),
            'blocked'  => trim($_REQUEST['blocked_filter'])
        );

        parent::__construct($SoapEngine);

        $this->sortElements = array(
            'changeDate'  => 'Change date',
            'description' => 'Description',
            'ip'          => 'Address'
        );
    }

    function listRecords()
    {

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter = array(
            'ip' => $this->filters['ip'],
            'description'   => $this->filters['description'],
            'tenant' => $this->filters['tenant'],
            'msteams' => 1 == intval($this->filters['msteams']),
            'blocked' => 1 == intval($this->filters['blocked'])
        );

        // Range
        $range = array(
            'start' => intval($this->next),
            'count' => intval($this->maxrowsperpage)
        );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'description';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

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

        // Call function
        $this->log_action('getTrustedPeers');

        $result = $this->SoapEngine->soapclient->getTrustedPeers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $this->rows = $result->total;

            if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print <<< END
<div class="alert alert-success"><center>$this->rows records found</center></div>
    <table class='table table-striped table-condensed' width=100%>
    <thead>
    <tr>
        <td><b>Id</b></th>
        <td><b>Owner</b></td>
        <td><b>Trusted peer</b></td>
        <td><b>Blocked</b></td>
        <td><b>Prepaid</b></td>
        <td><b>Capacity</b></td>
        <td><b>MS Teams</b></td>
        <td><b>Tenant</b></td>
        <td><b>Carrier</b></td>
        <td><b>Originator</b></td>
        <td><b>Description</b></td>
        <td><b>Strip</b></td>
        <td><b>Prefix</b></td>
        <td><b>Change date</b></td>
        <td><b>Actions</b></td>
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
                while ($i < $maxrows)  {

                    if (!$result->peers[$i]) break;

                    $peer = $result->peers[$i];

                    $index = $this->next+$i+1;

                    $base_url_data = array(
                        'service' => $this->SoapEngine->service,
                        'ip_filter' => $peer->ip,
                        'msteams_filter' => $peer->msteams
                    );

                    $delete_url_data = array_merge(
                        $base_url_data,
                        array(
                            'action' => 'Delete'
                        )
                    );

                    $update_url_data = $base_url_data;

                    $customer_url_data = array(
                        'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                        'customer_filter' => $peer->reseller
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['ip_filter'] == $peer->ip) {
                        $delete_url_data['confirm'] = 1;
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $delete_url = $this->buildUrl($delete_url_data);
                    $update_url = $this->buildUrl($update_url_data);
                    $_customer_url = $this->buildUrl($customer_url_data);

                    if ($peer->msteams) {
                        $msteams = 'Yes';
                    } else {
                        $msteams = 'No';
                    }

                    if ($peer->prepaid) {
                        $prepaid = 'Yes';
                    } else {
                        $prepaid = 'No';
                    }

                    printf(
                        "
                        <tr>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        <td><a href=%s>%s</a></td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td>%s</td>
                        <td><a href=%s>%s</a></td>
                        </tr>
                        ",
                        $index,
                        $_customer_url,
                        $peer->reseller,
                        $update_url,
                        $peer->ip,
                        $peer->blocked ? 'Yes' : 'No',
                        $prepaid,
                        $peer->callLimit,
                        $msteams,
                        $peer->tenant,
                        $peer->carrierName,
                        $peer->originator,
                        $peer->description,
                        $peer->strip,
                        $peer->prefix,
                        $peer->changeDate,
                        $delete_url,
                        $actionText
                    );

                    $i++;
                }
            }

            print "</table>";

            if ($result->total == 1) {
                $this->showRecord($peer);
            }
            $this->showPagination($maxrows);

            return true;
        }
    }

    function showRecord($peer)
    {
        print <<< END
<table border=0 cellpadding=10>
    <tr>
        <td valign=top>
            <table border=0>
END;
        printf("<form method=post name=addform action=%s>", $_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
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
                        <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                        </tr>
                        ",
                        $item_name,
                        $item,
                        $peer->$item
                    );
                } elseif ($this->FieldsAdminOnly[$item]['type'] == 'boolean') {
                    if ($peer->$item == 1) {
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
                        $peer->$item
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
                    $peer->$item
                );
            } elseif ($this->Fields[$item]['type'] == 'boolean') {
                if ($peer->$item == 1) {
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
                    $peer->$item
                );
            }
        }

        print <<< END
<tr>
    <td colspan=2><input type=submit value=Update></td>
</tr>
END;
        printf("<input type=hidden name=ip_filter value='%s'>", $peer->ip);
        $this->printFiltersToForm();

        $this->printHiddenFormElements();

        print <<< END
    </form>
</table>
END;
    }

    function showAddForm()
    {
        //if ($this->selectionActive) return;

        printf("<form class=form-inline method=post name=addform action=%s>", $_SERVER['PHP_SELF']);
        print <<< END
<div class='well well-small'>
    <input class='btn btn-warning' type=submit name=action value=Add>
END;
        $this->showCustomerTextBox();
        if ($this->filters['msteams']) {
            $checked = 'checked';
        } else {
            $checked = '';
        }

        print <<< END
    <div class='input-prepend'>
        <span class='add-on'>Address</span><input class=span2 type=text size=20 name=ipaddress>
    </div>
    <div class='input-prepend'>
        <span class='add-on'>Call limit</span><input class=span1 type=text size=4 name=callLimit value=30>
    </div>
    <div class='input-prepend'>
        <span class='add-on'>Description</span><input class=span2 type=text size=30 name=description>
    </div>
END;
        printf(
            "
            <div class='input-prepend'>
            <span class='add-on'>Tenant</span><input class=span1 type=text size=20 name=tenant value=%s>
            </div>
            ",
            $this->filters['tenant']
        );
        printf(
            "
            <label>
                <input type=checkbox value=1 name=msteams_filter %s> MS Teams
            </label>
            ",
            $checked
        );

        $this->printHiddenFormElements();

        print <<< END
        </div>
        </form>
    </tr>
</table>
END;
    }

    function addRecord($dictionary = array())
    {
        if ($dictionary['ipaddress']) {
            $ipaddress   = $dictionary['ipaddress'];
        } else {
            $ipaddress   = trim($_REQUEST['ipaddress']);
        }

        if ($dictionary['msteams']) {
            $msteams   = $dictionary['msteams'];
        } else {
            $msteams   = trim($_REQUEST['msteams']);
        }

        $this->filters['msteams'] = $msteams;

        if ($dictionary['description']) {
            $description   = $dictionary['description'];
        } else {
            $description   = trim($_REQUEST['description']);
        }

        if ($dictionary['tenant']) {
            $tenant = $dictionary['tenant'];
        } else {
            $tenant = trim($_REQUEST['tenant']);
        }

        if ($dictionary['carrierName']) {
            $carrierName = $dictionary['carrierName'];
        } else {
            $carrierName = trim($_REQUEST['carrierName']);
        }

        if ($dictionary['prefix']) {
            $prefix = $dictionary['prefix'];
        } else {
            $prefix = trim($_REQUEST['prefix']);
        }

        if ($dictionary['strip']) {
            $strip = $dictionary['strip'];
        } else {
            $strip = trim($_REQUEST['strip']);
        }

        if ($dictionary['originator']) {
            $originator = $dictionary['originator'];
        } else {
            $originator = trim($_REQUEST['originator']);
        }

        if ($dictionary['callLimit']) {
            $callLimit   = $dictionary['callLimit'];
        } else {
            $callLimit   = trim($_REQUEST['callLimit']);
        }

        if ($dictionary['owner']) {
            $owner   = $dictionary['owner'];
        } else {
            $owner   = trim($_REQUEST['owner']);
        }

        list($customer, $reseller)=$this->customerFromLogin($dictionary);

        if (!strlen($ipaddress)) {
            print "<p><font color=red>Error: Missing IP or description. </font>";
            return false;
        }

        $peer = array(
            'ip'          => $ipaddress,
            'description' => $description,
            'callLimit'   => intval($callLimit),
            'msteams'     => 1 == $msteams,
            'tenant'      => $tenant,
            'carrierName' => $carrierName,
            'originator'  => $originator,
            'prefix'      => $prefix,
            'strip'       => intval($strip),
            'blocked'     => 0,
            'owner'       => intval($_REQUEST['owner']),
            'customer'    => intval($customer),
            'reseller'    => intval($reseller)
        );

        $function = array(
            'commit'   => array(
                'name'       => 'addTrustedPeer',
                'parameters' => array($peer),
                'logs'       => array(
                    'success' => sprintf('Trusted peer %s has been added', $ipaddress)
                )
            )
        );

        return $this->SoapEngine->execute($function, $this->html);
    }

    function updateRecord()
    {
        list($customer, $reseller)=$this->customerFromLogin($dictionary);

        if (!strlen($this->filters['ip'])) {
            print "<p><font color=red>Error: missing peer address. </font>";
            return false;
        }
        $peer = array(
            'ip'          => $this->filters['ip'],
            'description' => $_REQUEST['description_form'],
            'authToken'   => $_REQUEST['authToken_form'],
            'tenant'      => $_REQUEST['tenant_form'],
            'carrierName' => $_REQUEST['carrierName_form'],
            'originator'  => $_REQUEST['originator_form'],
            'prefix'      => $_REQUEST['prefix_form'],
            'strip'       => intval($_REQUEST['strip_form']),
            'callLimit'   => intval($_REQUEST['callLimit_form']),
            'prepaid'     => 1 == $_REQUEST['prepaid_form'],
            'blocked'     => 1 == $_REQUEST['blocked_form'],
            'msteams'     => 1 == $_REQUEST['msteams_form'],
            'customer'    => intval($customer),
            'reseller'    => intval($reseller)
        );

        $function = array(
            'commit'   => array(
                'name'       => 'updateTrustedPeer',
                'parameters' => array($peer),
                'logs'       => array(
                    'success' => sprintf('Trusted peer %s has been updated', $this->filters['ip'])
                )
            )
        );

        return $this->SoapEngine->execute($function, $this->html);
    }

    function deleteRecord($dictionary = array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if (!strlen($this->filters['ip'])) {
            print "<p><font color=red>Error: missing IP address. </font>";
            return false;
        }

        $function = array(
            'commit'   => array(
                'name'       => 'deleteTrustedPeer',
                'parameters' => array($this->filters['ip']),
                'logs'       => array(
                    'success' => sprintf('Trusted peer %s has been deleted', $this->filters['ip'])
                )
            )
        );

        unset($this->filters);
        return $this->SoapEngine->execute($function, $this->html);
    }

    function showSeachFormCustom()
    {
        if (intval($this->filters['msteams'])  == 1) {
            $checked_msteams = 'checked';
        } else {
            $checked_msteams = '';
        }

        printf(
            "
            <div class='input-prepend'>
            <span class='add-on'>Address</span><input class=span2 type=text size=20 name=ip_filter value='%s'>
            </div>
            ",
            $this->filters['ip']
        );
        printf(
            "
            <div class='input-prepend'>
            <span class='add-on'>Description</span><input type=text class=span2 size=20 name=description_filter value='%s'>
            </div>
            ",
            $this->filters['description']
        );
        printf(
            "
            <div class='input-prepend'>
            <span class='add-on'>Tenant</span><input type=text class=span1 size=10 name=tenant_filter value='%s'>
            </div>
            ",
            $this->filters['tenant']
        );
        printf(
            "
            <label class='checkbox'>
                <input type=checkbox value=1 name=blocked_filter %s> Blocked
            </label>
            ",
            $this->filters['blocked'] == 1 ? 'checked' : ''
        );
        printf(
            "
            <label class='checkbox'>
                <input type=checkbox value=1 name=msteams_filter %s> MS Teams
            </label;>
            ",
            $checked_msteams
        );
    }

    function showCustomerTextBox()
    {
        print "<div class='input-prepend'><span class='add-on'>Owner</span>";
        $this->showResellerForm('reseller');
        print "</div>";
    }

    function showTextBeforeCustomerSelection()
    {
        print "Owner";
    }

    function showCustomerForm($name = 'customer_filter')
    {
    }
}
