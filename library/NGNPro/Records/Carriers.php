<?php

class Carriers extends Records
{
    var $carriers=array();

    var $Fields=array(
                              'id'         => array('type'=>'integer',
                                                   'readonly' => true),
                              'name'       => array('type'=>'string')
                              );

    var $sortElements=array(
                            'changeDate' => 'Change date',
                            'name'       => 'Carrier'
                            );

    public function __construct($SoapEngine)
    {
        $this->filters   = array(
            'id'   => trim($_REQUEST['id_filter']),
            'name' => trim($_REQUEST['name_filter'])
        );
        parent::__construct($SoapEngine);
    }

    function showCustomerTextBox () {
        print "Reseller";
        print "</span>";
        $this->showResellerForm('reseller');
        print "</div>";
    }

    function listRecords() {

        $this->showSeachForm();

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);

        // Filter
        $filter=array(
                      'id'       => intval($this->filters['id']),
                      'name'     => $this->filters['name'],
                      'reseller' => intval($this->filters['reseller'])
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

        // Call function
        $this->log_action('getCarriers');
        $result  = $this->SoapEngine->soapclient->getCarriers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {

            $this->rows = $result->total;

            print "
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-condensed table-striped' width=100%>
            ";

            print "
            <thead>
            <tr>
                <th>Id</th>
                <th>Owner</th>
                <th>Carrier</th>
                <th>Name</th>
                <th>Gateways</th>
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
                    if (!$result->carriers[$i]) break;

                    $carrier = $result->carriers[$i];

                    $index=$this->next+$i+1;

                    $_delete_url = $this->url.sprintf("&service=%s&action=Delete&id_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($carrier->id)
                    );

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['id_filter'] == $carrier->id) {
                        $_delete_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    $_url = $this->url.sprintf("&service=%s&id_filter=%s&reseller_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($carrier->id),
                    urlencode($carrier->reseller)
                    );

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($carrier->reseller)
                    );

                    $_gateway_url = $this->url.sprintf("&service=pstn_gateways@%s&carrier_id_filter=%d&reseller_filter=%s",
                    urlencode($this->SoapEngine->soapEngine),
                    urlencode($carrier->id),
                    urlencode($carrier->reseller)
                    );

                    printf("
                    <tr>
                    <td>%s</td>
                    <td><a href=%s>%s</a></td>
                    <td><a href=%s>%s</a></td>
                    <td>%s</td>
                    <td><a href=%s>Gateways</a></td>
                    <td>%s</td>
                    <td><a class='btn-small btn-danger' href=%s>%s</a></td>
                    </tr>",
                    $index,
                    $_customer_url,
                    $carrier->reseller,
                    $_url,
                    $carrier->id,
                    $carrier->name,
                    $_gateway_url,
                    $carrier->changeDate,
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
                $this->showRecord($carrier);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function showAddForm() {
        //if ($this->selectionActive) return;
        printf ("<form class=form-inline method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "
        <div class='well well-small'>
        ";

        print "
        <input class='btn btn-warning' type=submit name=action value=Add>
        ";
        print "<div class='input-prepend'><span class='add-on'>";
        $this->showCustomerTextBox();

        printf (" <div class='input-prepend'><span class='add-on'>Name</span><input type=text size=20 name=name></div>");

        $this->printHiddenFormElements();

        print "</div>
        </form>
        ";
    }

    function addRecord($dictionary=array()) {

        if ($dictionary['name']) {
            $name=$dictionary['name'];
        } else {
            $name = trim($_REQUEST['name']);
        }

        list($customer,$reseller)=$this->customerFromLogin($dictionary);

        $structure=array('name'     => $name,
                         'reseller' => intval($reseller)
                         );


        if (!strlen($name)) {
            printf ("<p><font color=red>Error: Missing name. </font>");
            return false;
        }

        $function=array('commit'   => array('name'       => 'addCarrier',
                                            'parameters' => array($structure),
                                            'logs'       => array('success' => sprintf('Carrier %s has been added',$name)))
                        );

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
            print "<p><font color=red>Error: missing carrier id </font>";
            return false;
        }

        $function=array('commit'   => array('name'       => 'deleteCarrier',
                                            'parameters' => array(intval($id)),
                                            'logs'       => array('success' => sprintf('Carrier %d has been deleted',$id)))
                        );

        unset($this->filters);
        return $this->SoapEngine->execute($function,$this->html);
    }

    function showSeachFormCustom() {
        printf (" <div class='input-prepend'><span class='add-on'>Carrier</span><input type=text size=10 name=id_filter value='%s'></div>",$this->filters['id']);
        printf (" <div class='input-prepend'><span class='add-on'>Name</span><input type=text size=20 name=name_filter value='%s'></div>",$this->filters['name']);
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
        if (!$this->sorting['sortBy'])    $this->sorting['sortBy']    = 'name';
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
        $this->log_action('getCarriers');
        $result = $this->SoapEngine->soapclient->getCarriers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            if ($result->carriers[0]){
                return $result->carriers[0];
            } else {
                return false;
            }
        }
    }

    function showRecord($carrier) {

        print "<h3>Carrier</h3>";


        printf ("<form class=form-horizontal method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            printf ("<div class=control-group><label class=control-label>%s</label>
            <td class=border>",
            $item_name
            );

            if ($this->Fields[$item]['readonly']) {
                printf ("<div class=controls style='padding-top:5px'><input name=%s_form type=hidden value='%s'>%s</div>",
                $item,
                $carrier->$item,
                $carrier->$item
                );
            } else {
                printf ("<div class=controls><input class=span2 name=%s_form type=text value='%s'></div>",
                $item,
                $carrier->$item
                );
            }
            print "
            </div>";
        }

        printf ("<input type=hidden name=id_filter value='%s'>",$carier->id);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        <div class='from-actions'>
        <input class='btn btn-warning' type=submit value=Update>
        </div>
        ";
        print "</form>";

    }

    function updateRecord () {
        //print "<p>Updating carrier ...";

        if (!$_REQUEST['id_filter']) return;

        if (!$carrier = $this->getRecord($_REQUEST['id_filter'])) {
            return false;
        }

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            if ($this->Fields[$item]['type'] == 'integer') {
                $carrier->$item = intval($_REQUEST[$var_name]);
            } else {
                $carrier->$item = trim($_REQUEST[$var_name]);
            }
        }

        $function=array('commit'   => array('name'       => 'updateCarrier',
                                            'parameters' => array($carrier),
                                            'logs'       => array('success' => sprintf('Carrier %d has been updated',$_REQUEST['id_filter'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result);
        return (bool)$result;
    }
}

