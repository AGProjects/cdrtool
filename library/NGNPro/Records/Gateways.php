<?php

class Gateways extends Records
{
    var $carriers = array();
    var $FieldsReadOnly = array(
        'reseller',
        'changeDate'
    );

    var $Fields = array(
        'id'         => array('type'=>'integer',
            'readonly' => true),
        'name'       => array('type'=>'string'),
        'carrier_id' => array('type'=>'integer'),
        'transport'  => array('type'=>'string'),
        'ip'         => array('name'=>'IP or hostname',
            'type'=>'string'),
        'port'       => array('type'=>'integer')
    );

    //var $transports = array('udp','tcp','tls');
    var $transports = array('udp');

    public function __construct($SoapEngine)
    {
        $this->filters   = array(
            'id'         => trim($_REQUEST['id_filter']),
            'name'       => trim($_REQUEST['name_filter']),
            'carrier_id' => trim($_REQUEST['carrier_id_filter'])
        );

        $this->sortElements = array(
            'changeDate'  => 'Change date',
            'name'        => 'Gateway',
            'carrier_id'  => 'Carrier',
            'ip'          => 'Address'
        );

        parent::__construct($SoapEngine);
    }

    function listRecords()
    {
        $this->getCarriers();

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter = array(
            'id'        => intval($this->filters['id']),
            'name'      => $this->filters['name'],
            'carrier_id'=> intval($this->filters['carrier_id']),
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
            'filter'  => $filter,
            'orderBy' => $orderBy,
            'range'   => $range
        );

        // Call function
        $this->log_action('getGateways');
        $result = $this->SoapEngine->soapclient->getGateways($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            $this->rows = $result->total;

            print <<< END
<div class="alert alert-success"><center>$this->rows records found</center></div>
<p>
<table class='table table-striped table-condensed' width=100%>
    <thead>
    <tr>
        <th>Id</th>
        <th>Owner</th>
        <th>Gateway</th>
        <th>Carrier</th>
        <th>Name</th>
        <th>Address</th>
        <th>Rules</th>
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
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows) {
                    if (!$result->gateways[$i]) break;

                    $gateway = $result->gateways[$i];

                    $index = $this->next + $i + 1;

                    $base_url_data = array(
                        'service' => $this->SoapEngine->service,
                        'id_filter' => $gateway->id,
                    );

                    $delete_url_data = array_merge(
                        $base_url_data,
                        array(
                            'action' => 'Delete',
                        )
                    );

                    $customer_url_data = array(
                        'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                        'customer_filter' => $gateway->reseller
                    );

                    $carrier_url_data = array_merge(
                        $base_url_data,
                        array(
                            'service' => sprintf('pstn_carriers@%s', $this->SoapEngine->soapEngine),
                            'id_filter' => $gateway->carrier_id,
                            'reseller_filter' => $gateway->reseller,
                        )
                    );

                    $rules_url_data = array(
                        'service' => sprintf('gateway_rules@%s', $this->SoapEngine->soapEngine),
                        'gateway_id_filter' => $gateway->id,
                        'reseller_filter' => $gateway->reseller
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $gateway->id) {
                        $delete_url_data['confirm'] = 1;
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_delete_url = $this->buildUrl($delete_url_data);
                    $_url = $this->buildUrl($base_url_data);
                    $_customer_url = $this->buildUrl($customer_url_data);
                    $_carrier_url = $this->buildUrl($carrier_url_data);
                    $_rules_url = $this->buildUrl($rules_url_data);

                    printf(
                        "
                        <tr>
                        <td valign=top>%s</td>
                        <td valign=top><a href=%s>%s</a></td>
                        <td valign=top><a href=%s>%s</a></td>
                        <td valign=top><a href=%s>%s</a></td>
                        <td valign=top>%s</td>
                        <td valign=top>%s:%s:%s</td>
                        <td valign=top><a href=%s>Rules</a></td>
                        <td valign=top>%s</td>
                        <td valign=top><a class='btn-small btn-danger' href=%s>%s</a></td>
                        </tr>
                        ",
                        $index,
                        $_customer_url,
                        $gateway->reseller,
                        $_url,
                        $gateway->id,
                        $_carrier_url,
                        $gateway->carrier,
                        $gateway->name,
                        $gateway->transport,
                        $gateway->ip,
                        $gateway->port,
                        $_rules_url,
                        $gateway->changeDate,
                        $_delete_url,
                        $actionText
                    );

                    printf("
                    </tr>
                    ");

                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($gateway);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm()
    {
        //if ($this->selectionActive) return;

        $this->getCarriers();

        if (!count($this->carriers)) {
            print "<p>Create a carrier first";
            return false;
        }

        printf("<form class='form-inline' method=post name=addform action=%s>", $_SERVER['PHP_SELF']);
        print <<< END
<div class='well well-small'>
    <input class='btn btn-warning' type=submit name=action value=Add>
    Carrier
    <select name=carrier_id>
END;
        foreach (array_keys($this->carriers) as $_carrier) {
            printf("<option value='%s'>%s", $_carrier, $this->carriers[$_carrier]);
        }

        print <<< END
    </select>
    <div class=input-prepend><span class="add-on">Name</span><input class=span2 type=text size=20 name=name></div>
    <div class=input-prepend><span class="add-on">Transport</span>
        <select class=span1 name=transport>
END;
        foreach ($this->transports as $_transport) {
            printf("<option value='%s'>%s", $_transport, $_transport);
        }

        print <<< END
        </select>
    </div>
    <div class=input-prepend>
        <span class="add-on">Address</span><input class=span2 type=text size=25 name=address>
    </div>
END;
        $this->printHiddenFormElements();

        print "</div>
            </form>
        ";
    }

    function addRecord($dictionary = array())
    {
        if ($dictionary['name']) {
            $name   = $dictionary['name'];
        } else {
            $name   = trim($_REQUEST['name']);
        }

        if ($dictionary['carrier_id']) {
            $carrier_id   = $dictionary['carrier_id'];
        } else {
            $carrier_id   = trim($_REQUEST['carrier_id']);
        }

        if ($dictionary['address']) {
            $address   = $dictionary['address'];
        } else {
            $address   = trim($_REQUEST['address']);
        }

        if ($dictionary['transport']) {
            $transport   = $dictionary['transport'];
        } else {
            $transport   = trim($_REQUEST['transport']);
        }

        if (!strlen($name) || !strlen($carrier_id) || !strlen($address)) {
            printf("<p><font color=red>Error: Missing gateway name, carrier_id or address</font>");
            return false;
        }

        $address_els = explode(':', $address);

        if (count($address_els) == 1) {
            $ip   = $address_els[0];
            $port ='5060';
        } elseif (count($address_els) == 2) {
            $ip   = $address_els[0];
            $port = $address_els[1];
        }

        if (!$port) $port = 5060;

        if (!in_array($transport, $this->transports)) {
            $transport=$this->transports[0];
        }

        $gateway = array(
            'name'       => $name,
            'carrier_id' => intval($carrier_id),
            'ip'         => $ip,
            'port'       => intval($port),
            'transport'  => $transport
        );

        $function = array(
            'commit'   => array(
                'name'       => 'addGateway',
                'parameters' => array($gateway),
                'logs'       => array('success' => sprintf('Gateway %s has been added', $name))
            )
        );

        unset($this->filters);
        return $this->SoapEngine->execute($function, $this->html);
    }

    function deleteRecord($dictionary = array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['id']) {
            $id   = $dictionary['id'];
        } else {
            $id   = trim($this->filters['id']);
        }

        if (!strlen($id)) {
            print "<p><font color=red>Error: missing gateway id.</font>";
            return false;
        }

        $function = array(
            'commit'   => array(
                'name'       => 'deleteGateway',
                'parameters' => array(intval($id)),
                'logs'       => array('success' => sprintf('Gateway %d has been deleted', $id))
            )
        );
        unset($this->filters);
        return $this->SoapEngine->execute($function, $this->html);
    }

    function showSeachFormCustom()
    {
        printf(
            "
            <div class=input-prepend>
            <span class=\"add-on\">Gateway</span>
            <input class=2 type=text size=10 name=id_filter value='%s'>
            </div>
            ",
            $this->filters['id']
        );

        if (count($this->carriers) > 250) {
            printf(
                "
                <div class='input-prepend'>
                <span class='add-on'>Carrier ID</span><input class=span1 type=text size=15 name=carrier_id_filter value='%s'>
                </div>
                ",
                $this->filters['carrier_id']
            );
        } else {
            print <<< END
            <select class=span2 name=carrier_id_filter>
                <option value=''>Carrier
END;
            $selected_carrier[$this->filters['carrier_id']] = 'selected';
            foreach (array_keys($this->carriers) as $_carrier) {
                printf(
                    "<option value='%s' %s>Id %s: %s</option>",
                    $_carrier,
                    $selected_carrier[$_carrier],
                    $_carrier,
                    $this->carriers[$_carrier]
                );
            }
            print "</select>";
        }

        printf(
            "
            <div class=input-prepend>
            <span class=\"add-on\">Name</span><input type=text size=20 name=name_filter value='%s'>
            </div>
            ",
            $this->filters['name']
        );
    }

    function showCustomerForm($name = 'customer_filter')
    {
    }

    function showTextBeforeCustomerSelection()
    {
        print "Owner";
    }

    function showRecord($gateway)
    {
        print "<h3>Gateway</h3>";

        printf("<form class=form-horizontal method=post name=addform action=%s>", $_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name = ucfirst($item);
            }

            printf(
                "<div class=control-group><label class=control-label>%s</label>",
                $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf(
                    "<div class=controls style='padding-top:5px'><input name=%s_form type=hidden value='%s'>%s</div>",
                    $item,
                    $gateway->$item,
                    $gateway->$item
                );
            } else {
                if ($item == 'carrier_id') {
                    printf("<div class=controls><select class=span2 name=%s_form>", $item);
                    $selected_carrier[$gateway->$item]='selected';
                    foreach (array_keys($this->carriers) as $_carrier) {
                        printf(
                            "<option value='%s' %s>%s",
                            $_carrier,
                            $selected_carrier[$_carrier],
                            $this->carriers[$_carrier]
                        );
                    }
                    print "</select></div>";
                } elseif ($item == 'transport') {
                    printf("<div class=controls><select class=span2 name=%s_form>", $item);
                    $selected_transport[$gateway->$item]='selected';
                    foreach ($this->transports as $_transport) {
                        printf("<option value='%s' %s>%s", $_transport, $selected_transport[$_transport], $_transport);
                    }

                    print "</select></div>";
                } else {
                    printf(
                        "<div class=controls><input class=span2 name=%s_form size=30 type=text value='%s'></div>",
                        $item,
                        $gateway->$item
                    );
                }
            }
            print "</div>";
        }

        printf("<input type=hidden name=id_filter value='%s'>", $gateway->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        <div class=form-actions>
        <input class='btn btn-warning' type=submit value=Update>
        </div>
        ";
        print "</form>";
    }

    function updateRecord()
    {
        //print "<p>Updating gateway ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$gateway = $this->getRecord($_REQUEST['id_filter'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $gateway->$item = intval($_REQUEST[$var_name]);
            } else {
                $gateway->$item = trim($_REQUEST[$var_name]);
            }
        }

        if (!in_array($gateway->transport, $this->transports)) {
            printf("<font color=red>Invalid transport '%s'</font>", $gateway->transport);
            return false;
        }

        $function = array(
            'commit'   => array(
                'name'       => 'updateGateway',
                'parameters' => array($gateway),
                'logs'       => array(
                    'success' => sprintf('Gateway %s has been updated', $_REQUEST['name_filter'])))
        );

        $result = $this->SoapEngine->execute($function, $this->html);

        dprint_r($result);
        return (bool)$result;
    }

    function getRecord($id)
    {
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter = array('id'  => intval($id));

        // Range
        $range = array(
            'start' => 0,
            'count' => 1
        );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'name';
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
        $this->log_action('getGateways');
        $result     = $this->SoapEngine->soapclient->getGateways($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if ($result->gateways[0]) {
                return $result->gateways[0];
            } else {
                return false;
            }
        }
    }
}
