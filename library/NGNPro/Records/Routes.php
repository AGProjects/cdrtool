<?php

class Routes extends Records {
    var $carriers=array();

    var $Fields=array(
                              'id'          => array('type'=>'integer',
                                                     'readonly' => true),
                              'carrier_id'  => array('type'=>'integer','name'=>'Carrier'),
                              'prefix'      => array('type'=>'string'),
                              'originator'  => array('type'=>'string'),
                              'priority'    => array('type'=>'integer')
                              );

    var $sortElements=array(
                            'prefix'       => 'Prefix',
                            'priority'     => 'Priority'
                            );

    public function __construct($SoapEngine)
    {
        $this->filters   = array(
            'prefix'    => trim($_REQUEST['prefix_filter']),
            'priority'  => trim($_REQUEST['priority_filter']),
            'carrier_id'=> trim($_REQUEST['carrier_id_filter']),
            'reseller'  => trim($_REQUEST['reseller_filter']),
            'id'        => trim($_REQUEST['id_filter'])
        );
        parent::__construct($SoapEngine);
    }

    function listRecords() {
        $this->getCarriers();

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array('prefix'       => $this->filters['prefix'],
                      'carrier_id'   => intval($this->filters['carrier_id']),
                      'reseller'     => intval($this->filters['reseller']),
                      'id'           => intval($this->filters['id'])
                      );


        // Range
        $range=array('start' => intval($this->next),
                     'count' => intval($this->maxrowsperpage)
                     );

        // Order
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'prefix';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $this->log_action('getRoutes');
        $result     = $this->SoapEngine->soapclient->getRoutes($Query);

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
            <table class='table table-condensed table-striped'width=100%>
            ";

            print "<thead>
            <tr>
                <th><b>Id</b></th>
                <th><b>Owner</b></th>
                <th><b>Route</b></th>
                <th><b>Carrier</b></th>
                <th><b>Gateways</b></th>
                <th><b>Prefix</b></th>
                <th><b>Originator</b></th>
                <th><b>Priority</b></th>
                <th><b>Change date</b></th>
                <th><b>Actions</b></th>
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

                    if (!$result->routes[$i]) break;

                    $route = $result->routes[$i];

                    $index=$this->next+$i+1;

                    $_delete_url = $this->url.sprintf("&service=%s&action=Delete&id_filter=%d",
                    urlencode($this->SoapEngine->service),
                    urlencode($route->id)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $route->id) {
                        $_delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->url.sprintf("&service=%s&id_filter=%d",
                    urlencode($this->SoapEngine->service),
                    urlencode($route->id)
                    );

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($route->reseller)
                    );

                    $_carrier_url = $this->url.sprintf("&service=pstn_carriers@%s&id_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($route->carrier_id)
                    );

                    $_gateway_url = $this->url.sprintf("&service=pstn_gateways@%s&carrier_id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($route->carrier_id),
                    urlencode($route->reseller)
                    );

                    printf("
                    <tr>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>Gateways</a></td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td>%s</td>
                    <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                    </tr>",
                    $index,
                    $_customer_url,
                    $route->reseller,
                    $_url,
                    $route->id,
                    $_carrier_url,
                    $route->carrier,
                    $_gateway_url,
                    $route->prefix,
                    $route->originator,
                    $route->priority,
                    $route->changeDate,
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
                $this->showRecord($route);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;

        if (!count($this->carriers)) {
            print "<p>Create a carrier first";
            return false;
        }

        printf ("<form class=form-inline method=post name=addform action=%s><div class='well well-small'>",$_SERVER['PHP_SELF']);

        print "
        <input class='btn btn-warning' type=submit name=action value=Add>
        ";

        print "<div class='input-prepend'><span class='add-on'>";
        printf (" Carrier ");
        print "</span>";

        print "<select class=span2 name=carrier_id> ";
        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s'>%s",$_carrier,$this->carriers[$_carrier]);
        }
        printf (" </select></div>");

        printf (" <div class='input-prepend'><span class='add-on'>Prefix</span><input type=text size=20 name=prefix></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Originator</span><input type=text size=20 name=originator></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Priority</span><input type=text size=5 name=priority></div>");

        $this->printHiddenFormElements();

        print "</div>
        </form>
        ";
    }

    function addRecord($dictionary=array()) {
        if ($dictionary['prefix']) {
            $prefix   = $dictionary['prefix'];
        } else {
            $prefix   = trim($_REQUEST['prefix']);
        }

        if ($dictionary['carrier_id']) {
            $carrier_id   = $dictionary['carrier_id'];
        } else {
            $carrier_id   = trim($_REQUEST['carrier_id']);
        }

        if ($dictionary['originator']) {
            $originator   = $dictionary['originator'];
        } else {
            $originator   = trim($_REQUEST['originator']);
        }

        if ($dictionary['priority']) {
            $priority   = $dictionary['priority'];
        } else {
            $priority   = trim($_REQUEST['priority']);
        }

        if (!strlen($carrier_id)) {
            printf ("<p><font color=red>Error: Missing carrier id. </font>");
            return false;
        }

        $route=array(
                     'prefix'       => $prefix,
                     'originator'   => $originator,
                     'carrier_id'   => intval($carrier_id),
                     'priority'     => intval($priority)
                     );

        $routes=array($route);

        $function=array('commit'   => array('name'       => 'addRoutes',
                                            'parameters' => array($routes),
                                            'logs'       => array('success' => sprintf('Route %s has been added',$prefix)))
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
            print "<p><font color=red>Error: missing route id. </font>";
            return false;
        }

        $route=array('id'=> intval($id));

        $routes=array($route);

        $function=array('commit'   => array('name'       => 'deleteRoutes',
                                            'parameters' => array($routes),
                                            'logs'       => array('success' => sprintf('Route %s has been deleted',$prefix)))
                        );

        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showSeachFormCustom() {

        printf (" <div class='input-prepend'><span class='add-on'>Route</span><input type=text size=10 name=id_filter value='%s'></div>",$this->filters['id']);
        print "
        <select name=carrier_id_filter>
        <option value=''>Carrier";

        $selected_carrier[$this->filters['carrier_id']]='selected';

        foreach (array_keys($this->carriers) as $_carrier) {
            printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
        }

        print "</select>";
        printf (" <div class='input-prepend'><span class='add-on'>Prefix</span><input type=text size=15 name=prefix_filter value='%s'></div>",$this->filters['prefix']);

    }

    function showCustomerTextBox () {
        print "Owner";
        $this->showResellerForm('reseller');
    }

    function showCustomerForm($name='customer_filter') {
    }

    function showTextBeforeCustomerSelection() {
        print "Owner";
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
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'prefix';
        if (!$this->sorting['sortOrder']) $this->sorting['sortOrder'] = 'ASC';

        $orderBy = array('attribute' => $this->sorting['sortBy'],
                         'direction' => $this->sorting['sortOrder']
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                        'orderBy' => $orderBy,
                        'range'   => $range
                        );

        // Call function
        $this->log_action('getRoutes');
        $result     = $this->SoapEngine->soapclient->getRoutes($Query);

        if ((new PEAR)->isError($result)) {
            $error_msg  = $result->getMessage();
            $error_fault= $result->getFault();
            $error_code = $result->getCode();
            $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
            syslog(LOG_NOTICE, $log);
            return false;
        } else {
            if ($result->routes[0]){
                return $result->routes[0];
            } else {
                return false;
            }
        }
    }

    function showRecord($route) {

        print "<h3>Route</h3>";

        printf ("<form class=form-horizontal method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<div class=control-group>
            <label class=control-label>%s</label>
            ",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<div class=controls style='padding-top:5px'><input name=%s_form type=hidden value='%s'>%s</div>",
                $item,
                $route->$item,
                $route->$item
                );
            } else {
                if ($item == 'carrier_id') {
                    printf ("<div class=controls><select name=%s_form>",$item);
                    $selected_carrier[$route->$item]='selected';
                    foreach (array_keys($this->carriers) as $_carrier) {
                        printf ("<option value='%s' %s>%s",$_carrier,$selected_carrier[$_carrier],$this->carriers[$_carrier]);
                    }
                    printf (" </select></div>");

                } else {
                    printf ("<div class=controls><input name=%s_form type=text value='%s'></div>",
                    $item,
                    $route->$item
                    );
                }
            }
            print "</div>";
        }

        printf ("<input type=hidden name=id_filter value='%s'>",$carier->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        <div class='form-actions'>
        <input class='btn btn-warning' type=submit value=Update>
        </div>
        ";
        print "</form>";
    }

    function updateRecord () {
        //print "<p>Updating route ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$route = $this->getRecord($_REQUEST['id_filter'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $route->$item = intval($_REQUEST[$var_name]);
            } else {
                $route->$item = trim($_REQUEST[$var_name]);
            }
        }

        $routes=array($route);

        $function=array('commit'   => array('name'       => 'updateRoutes',
                                            'parameters' => array($routes),
                                            'logs'       => array('success' => sprintf('Route %d has been updated',$_REQUEST['id_filter'])))
                        );
        $result = $this->SoapEngine->execute($function,$this->html);

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
}

