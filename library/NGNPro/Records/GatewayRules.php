<?php

class GatewayRules extends Records
{
    var $carriers=array();
    var $FieldsReadOnly=array(
                              'reseller',
                              'changeDate'
                              );

    var $Fields=array(
                              'id'         => array('type'=>'integer','readonly' => true),
                              'gateway_id' => array('type'=>'integer','name' => 'Gateway'),
                              'prefix'     => array('type'=>'string'),
                              'strip'      => array('type'=>'integer'),
                              'prepend'    => array('type'=>'string'),
                              'minLength'  => array('type'=>'integer'),
                              'maxLength'  => array('type'=>'integer')
                              );

    public function __construct($SoapEngine)
    {
        $this->filters   = array(
            'id'         => trim($_REQUEST['id_filter']),
            'gateway_id' => trim($_REQUEST['gateway_id_filter']),
            'carrier_id' => trim($_REQUEST['carrier_id_filter']),
            'prefix'     => trim($_REQUEST['prefix_filter']),
        );

        $this->sortElements=array(
            'changeDate' => 'Change date',
            'gateway'    => 'Gateway',
            'carrier'    => 'Carrier',
            'prefix'     => 'Prefix'
        );
        parent::__construct($SoapEngine);
    }

    function listRecords() {
        $this->getCarriers();

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'         => intval($this->filters['id']),
                      'gateway_id' => intval($this->filters['gateway_id']),
                      'carrier_id' => intval($this->filters['carrier_id']),
                      'prefix'     => $this->filters['prefix'],
                      'customer'   => intval($this->filters['customer']),
                      'reseller'   => intval($this->filters['reseller'])
                      );

        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'changeDate';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        $this->log_action('getGatewayRules');
        $result     = $this->SoapEngine->soapclient->getGatewayRules($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-striped table-condensed' width=100%>
            ";

            print "
                <thead>
            <tr>
                <th></th>
                <th>Owner</th>
                <th>Rule</th>
                <th>Carrier</th>
                <th>Gateway</th>
                <th>Prefix</th>
                <th>Strip</th>
                <th>Prepend</th>
                <th>MinLength</th>
                <th>MaxLength</th>
                <th>Change date</th>
                <th>Actions</th>
            </tr>
            </thead>
            ";

            if (!$this->next)  $this->next=0;

            if ($this->rows > $this->maxrowsperpage)  {
                $maxrows = $this->maxrowsperpage + $this->next;
                if ($maxrows > $this->rows) $maxrows = $this->maxrowsperpage;
            } else {
                $maxrows=$this->rows;
            }

            $i=0;

            if ($this->rows) {
                while ($i < $maxrows)  {

                    if (!$result->gateway_rules[$i]) break;

                    $gateway_rule = $result->gateway_rules[$i];

                    $index=$this->next+$i+1;

                    $_delete_url = $this->url.sprintf("&service=%s&action=Delete&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($gateway_rule->id),
                    urlencode($gateway_rule->reseller)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $gateway_rule->id) {
                        $_delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->url.sprintf("&service=%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($gateway_rule->id),
                    urlencode($gateway_rule->reseller)
                    );

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($gateway_rule->reseller)
                    );

                    $_carrier_url = $this->url.sprintf("&service=pstn_carriers@%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($gateway_rule->carrier_id),
                    urlencode($gateway_rule->reseller)
                    );

                    $_gateway_url = $this->url.sprintf("&service=pstn_gateways@%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($gateway_rule->gateway_id),
                    urlencode($gateway_rule->reseller)
                    );

                    printf("
                    <tr>
                    <td valign=top>%s</td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a></td>
                    <td valign=top><a href=%s>%s</a> (%d)</td>
                    <td valign=top><a href=%s>%s (%d)</a></td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top>%s</td>
                    <td valign=top><a class='btn-small btn-danger' href=%s>%s</a></td>
                    </tr>",
                    $index,
                    $_customer_url, $gateway_rule->reseller,
                    $_url,     $gateway_rule->id,
                    $_carrier_url, $gateway_rule->carrier,$gateway_rule->carrier_id,
                    $_gateway_url, $gateway_rule->gateway,$gateway_rule->gateway_id,
                    $gateway_rule->prefix,
                    $gateway_rule->strip,
                    $gateway_rule->prepend,
                    $gateway_rule->minLength,
                    $gateway_rule->maxLength,
                    $gateway_rule->changeDate,
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
                $this->showRecord($gateway_rule);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;

        $this->getGateways();

        if (!count($this->gateways)) {
            print "<p>Create a gateway first";
            return false;
        }

        printf ("<form class=form-inline method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <div class='well well-small'>
        ";

        print "
        <input class='btn btn-warning' type=submit name=action value=Add>
        <input type=hidden name=sortBy value=changeDate>
        ";

        print "<div class='input-prepend'><span class='add-on'>Gateway</span><select class=span3 name=gateway_id>";
        foreach (array_keys($this->gateways) as $_gateway) {
            printf ("<option value='%s'>%s",$_gateway,$this->gateways[$_gateway]);
        }
        printf (" </select></div>");

        printf (" <div class='input-prepend'><span class='add-on'>Prefix</span><input class=span1 type=text size=15 name=prefix></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Strip</span><input class=span1 type=text size=5 name=strip></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Prepend</span><input class=span1 type=text size=15 name=prepend></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Min length</span><input class=span1 type=text size=5 name=minLength></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Max length</span><input class=span1 type=text size=5 name=maxLength></div>");

        $this->printHiddenFormElements();

        print "</div>
        </form>
        ";
    }

    function addRecord($dictionary=array()) {
        if ($dictionary['gateway_id']) {
            $gateway_id   = $dictionary['gateway_id'];
        } else {
            $gateway_id = trim($_REQUEST['gateway_id']);
        }

        if ($dictionary['prefix']) {
            $prefix   = $dictionary['prefix'];
        } else {
            $prefix   = trim($_REQUEST['prefix']);
        }

        if ($dictionary['strip']) {
            $strip   = $dictionary['strip'];
        } else {
            $strip   = trim($_REQUEST['strip']);
        }

        if ($dictionary['prepend']) {
            $prepend   = $dictionary['prepend'];
        } else {
            $prepend   = trim($_REQUEST['prepend']);
        }

        if ($dictionary['minLength']) {
            $minLength   = $dictionary['minLength'];
        } else {
            $minLength   = trim($_REQUEST['minLength']);
        }

        if ($dictionary['maxLength']) {
            $maxLength   = $dictionary['maxLength'];
        } else {
            $maxLength   = trim($_REQUEST['maxLength']);
        }

        if (!strlen($gateway_id)) {
            printf ("<p><font color=red>Error: Missing gateway id</font>");
            return false;
        }

        $rule=array(
                     'gateway_id' => intval($gateway_id),
                     'prefix'     => $prefix,
                     'prepend'    => $prepend,
                     'strip'      => intval($strip),
                     'minLength'  => intval($minLength),
                     'maxLength'  => intval($maxLength)
                    );

        $function=array('commit'   => array('name'       => 'addGatewayRule',
                                            'parameters' => array($rule),
                                            'logs'       => array('success' => sprintf('Gateway rule has been added')))
                        );

        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function deleteRecord($dictionary=array()) {
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
            print "<p><font color=red>Error: missing rule id </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteGatewayRule',
                                            'parameters' => array(intval($id)),
                                            'logs'       => array('success' => sprintf('Gateway rule %d has been deleted',$id)))
                        );
        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);

    }

    function showSeachFormCustom() {
        printf (" <div class='input-prepend'><span class='add-on'>Rule</span><input class=span2 type=text size=15 name=id_filter value='%s'></div>",$this->filters['id']);
        print "
        <select class=span2 name=carrier_id_filter>
        <option value=''>Carrier";

        $selected_carrier[$this->filters['carrier_id']]='selected';

        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
        }

        printf (" </select>");
        printf (" <div class='input-prepend'><span class='add-on'>Gateway</span><input class=span2 type=text size=15 name=gateway_id_filter value='%s'></div>",$this->filters['gateway_id']);
        printf (" <div class='input-prepend'><span class='add-on'>Prefix</span><input class=span1 type=text size=15 name=prefix_filter value='%s'></div>",$this->filters['prefix']);
    }

    function showCustomerForm($name='customer_filter') {
    }

    function showTextBeforeCustomerSelection() {
        print "Owner";
    }

    function showRecord($rule) {

        $this->getGateways();

        print "<h3>Rule</h3>";

        printf ("<form class=form-horizontal method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<div class='control-group'>
                <label class=control-label>
                %s</label>
            ",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<div class=controls style='padding-top:5px'><input name=%s_form type=hidden value='%s'>%s</div>",
                $item,
                $rule->$item,
                $rule->$item
                );
            } else {
                if ($item == 'gateway_id') {
                    printf ("<div class=controls><select class=span2 name=%s_form>",$item);
                    $selected_gateway[$rule->$item]='selected';

                    foreach (array_keys($this->gateways) as $_gateway) {
                        printf ("<option value='%s' %s>%s",$_gateway,$selected_gateway[$_gateway],$this->gateways[$_gateway]);
                    }

                    print "</select></div>";

                } else {
                    printf ("<div class=controls><input class=span2 name=%s_form size=30 type=text value='%s'></div>",
                    $item,
                    $rule->$item
                    );
                }
            }
            print "
            </div>";
        }

        printf ("<input type=hidden name=reseller_filter value='%s'>",$rule->reseller);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        <div class=form-actions>
        <input class='btn btn-warning' type=submit value=Update>
        </div>
        ";
        print "</form>";
    }

    function updateRecord () {
        //print "<p>Updating rule ...";

        if (!$_REQUEST['id_form'] || !strlen($_REQUEST['reseller_filter'])) {
            return;
        }

        if (!$rule = $this->getRecord($_REQUEST['id_form'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $rule->$item = intval($_REQUEST[$var_name]);
            } else {
                $rule->$item = trim($_REQUEST[$var_name]);
            }
        }

        $function=array('commit'   => array('name'       => 'updateGatewayRule',
                                            'parameters' => array($rule),
                                            'logs'       => array('success' => sprintf('Rule %d has been updated',$_REQUEST['id_form'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result)    ;
        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s</font>",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            return true;
        }
    }

    function getRecord($id) {
        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('id'  => intval($id));

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        $this->sorting['sortBy']    = 'gateway';
        $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $this->log_action('getGatewayRules');        
        $result     = $this->SoapEngine->soapclient->getGatewayRules($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            if ($result->gateway_rules[0]){
                return $result->gateway_rules[0];
            } else {
                return false;
            }
        }
    }
}

