<?php

class EnumMappings extends Records
{
    var $default_ttl = 3600;
    var $default_priority = 5;

    var $sortElements=array('changeDate' => 'Change date',
                            'number'     => 'Number',
                            'tld'        => 'TLD'
                            );

    var $ranges=array();

    var $FieldsReadOnly=array(
                              'customer',
                              'reseller'
                              );
    var $Fields=array(
                              'owner'    => array('type'=>'integer'),
                              'info'     => array('type'=>'string')
                              );

    var $mapping_fields=array('id'       => 'integer',
                              'type'     => 'string',
                              'mapto'    => 'string',
                              'priority' => 'integer',
                              'ttl'      => 'integer'
                              );

    var $NAPTR_services=array(
        "sip"    => array("service"=>"sip",
                              "webname"=>"SIP",
                              "schemas"=>array("sip:","sips:")),
        "mailto" => array("service"=>"mailto",
                              "webname"=>"Email",
                              "schemas"=>array("mailto:")),
        "web:http"   => array("service"=>"web:http",
                              "webname"=>"WEB (http)",
                              "schemas"=>array("http://")),
        "web:https"  => array("service"=>"web:https",
                              "webname"=>"WEB (https)",
                              "schemas"=>array("https://")),
        "x-skype:callto" => array("service"=>"x-skype:callto",
                              "webname"=>"Skype",
                              "schemas"=>array("callto:")),
        "h323"   => array("service"=>"h323",
                              "webname"=>"H323",
                              "schemas"=>array("h323:")),
        "iax"    => array("service"=>"iax",
                              "webname"=>"IAX",
                              "schemas"=>array("iax:")),
        "iax2"   => array("service"=>"iax2",
                              "webname"=>"IAX2",
                              "schemas"=>array("iax2:")),
        "mms"    => array("service"=>"mms",
                              "webname"=>"MMS",
                              "schemas"=>array("tel:","mailto:")),
        "sms"    => array("service"=>"sms",
                              "webname"=>"SMS",
                              "schemas"=>array("tel:","mailto:")),
        "ems"    => array("service"=>"ems",
                              "webname"=>"EMS",
                              "schemas"=>array("tel:","mailto:")),
        "im"     => array("service"=>"im",
                              "webname"=>"IM",
                              "schemas"=>array("im:")),
        "npd:tel"   => array("service"=>"npd+tel",
                              "webname"=>"Portability",
                              "schemas"=>array("tel:")),
        "void:mailto"  => array("service"=>"void:mailto",
                              "webname"=>"VOID(mail)",
                              "schemas"=>array("mailto:")),
        "void:http"  => array("service"=>"void:http",
                              "webname"=>"VOID(http)",
                              "schemas"=>array("http://")),
        "void:https" => array("service"=>"void:https",
                              "webname"=>"VOID(https)",
                              "schemas"=>array("https://")),
        "voice"  => array("service"=>"voice",
                              "webname"=>"Voice",
                              "schemas"=>array("voice:","tel:")),
        "tel"    => array("service"=>"tel",
                              "webname"=>"Tel",
                              "schemas"=>array("tel:")),
        "fax:tel"    => array("service"=>"fax:tel",
                              "webname"=>"Fax",
                              "schemas"=>array("tel:")),
        "ifax:mailto"   => array("service"=>"ifax:mailto",
                              "webname"=>"iFax",
                              "schemas"=>array("mailto:")),
        "pres"   => array("service"=>"pres",
                              "webname"=>"Presence",
                              "schemas"=>array("pres:")),
        "ft:ftp"    => array("service"=>"ft:ftp",
                              "webname"=>"FTP",
                              "schemas"=>array("ftp://")),
        "loc:http"  => array("service"=>"loc:http",
                              "webname"=>"GeoLocation",
                              "schemas"=>array("http://")),
        "key:http"  => array("service"=>"key:http",
                              "webname"=>"Public key",
                              "schemas"=>array("http://")),
        "key:https"  => array("service"=>"key:https",
                              "webname"=>"Public key (HTTPS)",
                              "schemas"=>array("https://"))
        );

    public function __construct($SoapEngine)
    {
        dprint("init EnumMappings");

        if ($_REQUEST['range_filter']) {
            list($_prefix, $_tld_filter)= explode("@",$_REQUEST['range_filter']);
            if ($_prefix && !$_REQUEST['number_filter']) {
                $_number_filter=$_prefix.'%';
            } else {
                $_number_filter=$_REQUEST['number_filter'];
            }
        } else {
            $_number_filter=$_REQUEST['number_filter'];
            $_tld_filter=trim($_REQUEST['tld_filter']);
        }

        $_number_filter=ltrim($_number_filter,'+');

        $this->filters   = array('number'       => ltrim($_number_filter,'+'),
                                 'tld'          => $_tld_filter,
                                 'range'        => trim($_REQUEST['range_filter']),
                                 'type'         => trim($_REQUEST['type_filter']),
                                 'mapto'        => trim($_REQUEST['mapto_filter']),
                                 'owner'        => trim($_REQUEST['owner_filter'])
        );
        parent::__construct($SoapEngine);
        $this->getAllowedDomains();
    }

    function listRecords()
    {
        $this->showSeachForm();

        $filter=array('number'   => $this->filters['number'],
                      'tld'      => $this->filters['tld'],
                      'type'     => $this->filters['type'],
                      'mapto'    => $this->filters['mapto'],
                      'owner'    => intval($this->filters['owner']),
                      'customer' => intval($this->filters['customer']),
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

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getNumbers');

        // Call function
        $result     = $this->SoapEngine->soapclient->getNumbers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {

            $this->rows = $result->total;

             if ($this->rows && $_REQUEST['action'] != 'PerformActions' && $_REQUEST['action'] != 'Delete') {
                $this->showActionsForm();
            }

            print "
            <div class=\"alert alert-success\"><center>$this->rows records found</center></div>
            <p>
            <table class='table table-condensed table-striped' width=100%>
            <thead>
            <tr>
                <th></th>
                <th>Range Owner</th>
                <th>Phone number</th>
                <th>TLD</th>
                <th>Info</th>
                <th>Owner</th>
                <th>Type</th>
                <th>Id</th>
                <th>Map to</th>
                <th>TTL</th>
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

                    if (!$result->numbers[$i]) break;

                    $number = $result->numbers[$i];
                    $index=$this->next+$i+1;

                    $j=1;

                    $base_url_data = array(
                        'service' => $this->SoapEngine->service,
                        'number_filter' => $number->id->number,
                        'tld_filter' => $number->id->tld
                    );

                    foreach ($number->mappings as $_mapping) {
                        unset($sip_engine);
                        foreach (array_keys($this->login_credentials['reseller_filters']) as $_res) {
                            if ($_res == $number->reseller) {
                                if ($this->login_credentials['reseller_filters'][$_res]['sip_engine']) {
                                    $sip_engine=$this->login_credentials['reseller_filters'][$_res]['sip_engine'];
                                    break;
                                }
                            }
                        }

                        if (!$sip_engine) {
                            if ($this->login_credentials['reseller_filters']['default']['sip_engine']) {
                                $sip_engine=$this->login_credentials['reseller_filters']['default']['sip_engine'];
                            } else {
                                $sip_engine=$this->SoapEngine->sip_engine;
                            }
                        }

                        if (preg_match("/^sip:(.*)$/", $_mapping->mapto, $m) && $this->sip_settings_page) {
                            $mapto_url_data = array(
                                'account' => $m[1],
                                'reseller' => $number->reseller,
                                'sip_engine' => $sip_engine
                            );

                            if ($this->adminonly) {
                                $mapto_url_data['adminonly'] = $this->adminonly;
                            }

                            foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
                                if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;

                                $mapto_url_data[$element] = $this->SoapEngine->extraFormElements[$element];
                            }

                            $mapto = sprintf(
                                "<a href=\"javascript:void(null);\" onClick=\"return window.open('%s?%s', 'SIP_Settings',
                                'toolbar=1,status=1,menubar=1,scrollbars=1,resizable=1,width=800,height=720')\">
                                sip:%s</a>",
                                $this->sip_settings_page,
                                http_build_query($mapto_url_data),
                                $m[1]
                            );
                        } else {
                            $mapto=sprintf("%s",$_mapping->mapto);
                        }

                        $delete_url_data = array_merge(
                            $base_url_data,
                            array(
                                'action' => 'Delete',
                                'mapto_filter' => $_mapping->mapto
                            )
                        );

                        if ($_REQUEST['action'] == 'Delete'
                            && $_REQUEST['number_filter'] == $number->id->number
                            && $_REQUEST['tld_filter'] == $number->id->tld
                            && $_REQUEST['mapto_filter'] == $_mapping->mapto
                        ) {
                            $delete_url_data['confirm'] = 1;
                            $actionText = "<font color=red>Confirm</font>";
                        } else {
                            $actionText = "Delete";
                        }

                        $_url = sprintf(
                            "%s&%s",
                            $this->url,
                            http_build_query($delete_url_data)
                        );
                        if ($j==1) {
                            $number_url_data = $base_url_data;

 		                   	if ($this->adminonly) $_number_url_data['reseller_filter'] = $number->reseller;

                            $_number_url = sprintf(
                                "%s&%s",
                                $this->url,
                                http_build_query($number_url_data)
                            );

                            $_customer_url = sprintf(
                                's&%s',
                                $this->url,
                                http_build_query(
                                    array(
                                        'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                                        'customer_filter' => $number->customer
                                    )
                                )
                            );

                            if ($number->owner) {
                                $_owner_url = sprintf(
                                    '<a href="%s&%s">%s</a>',
                                    $this->url,
                                    http_build_query(
                                        array(
                                            'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                                            'customer_filter' => $number->owner
                                        )
                                    ),
                                    $number->owner
                                );
                            } else {
                                $_owner_url='';
                            }

                            printf(
                                "
                                <tr>
                                <td>%s</td>
                                <td><a href=%s>%s.%s</a></td>
                                <td><a href=%s>+%s</a></td>
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
                                $number->customer,
                                $number->reseller,
                                $_number_url,
                                $number->id->number,
                                $number->id->tld,
                                $number->info,
                                $_owner_url,
                                ucfirst($_mapping->type),
                                $_mapping->id,
                                $mapto,
                                $_mapping->ttl,
                                $number->changeDate,
                                $_url,
                                $actionText
                            );
                        } else {
                            printf(
                                "
                                <tr>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td>%s</td>
                                <td><a href=%s>%s</a></td>
                                </tr>",
                                ucfirst($_mapping->type),
                                $_mapping->id,
                                $mapto,
                                $_mapping->ttl,
                                $number->changeDate,
                                $_url,
                                $actionText
                            );
                        }
                        $j++;
                    }

                    if (!is_array($number->mappings) || !count($number->mappings)) {
                        $delete_url_data = array_merge(
                            $base_url_data,
                            array(
                                'action' => 'Delete',
                                'mapto_filter' => $_mapping->mapto
                            )
                        );

                        if ($_REQUEST['action'] == 'Delete'
                            && $_REQUEST['number_filter'] == $number->id->number
                            && $_REQUEST['tld_filter'] == $number->id->tld
                            && $_REQUEST['mapto_filter'] == $_mapping->mapto
                        ) {
                            $delete_url_data['confirm'] = 1;
                            $actionText = "<font color=red>Confirm</font>";
                        } else {
                            $actionText = "Delete";
                        }

                        $_url = sprintf(
                            "%s&%s",
                            $this->url,
                            http_build_query($delete_url_data)
                        );

                        $number_url_data = $base_url_data;

                        if ($this->adminonly) $_number_url_data['reseller_filter'] = $number->reseller;

                        $_number_url = sprintf(
                            "%s&%s",
                            $this->url,
                            http_build_query($number_url_data)
                        );

                        $_customer_url = sprintf(
                            's&%s',
                            $this->url,
                            http_build_query(
                                array(
                                    'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                                    'customer_filter' => $number->customer
                                )
                            )
                        );

                        if ($number->owner) {
                            $_owner_url = sprintf(
                                '<a href="%s&%s">%s</a>',
                                $this->url,
                                http_build_query(
                                    array(
                                        'service' => sprintf('customers@%s', $this->SoapEngine->customer_engine),
                                        'customer_filter' => $number->owner
                                    )
                                ),
                                $number->owner
                            );
                        } else {
                            $_owner_url='';
                        }

                        printf(
                            "
                            <tr>
                            <td>%s</td>
                            <td><a href=%s>%s.%s</a></td>
                            <td><a href=%s>+%s</a></td>
                            <td>%s</td>
                            <td>%s</td>
                            <td>%s</td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td>%s</td>
                            <td><a href=%s>%s</a></td>
                            </tr>
                            ",
                            $index,
                            $_customer_url,
                            $number->customer,
                            $number->reseller,
                            $_number_url,
                            $number->id->number,
                            $number->id->tld,
                            $number->info,
                            $_owner_url,
                            $number->changeDate,
                            $_url,
                            $actionText
                        );
                    }

                    printf("
                    </tr>
                    ");

                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1 ) {
                $this->showRecord($number);
            } else {
                $this->showPagination($maxrows);
            }

            return true;
        }
    }

    function getLastNumber() {

        // Filter
        $filter=array('number' => ''
                      );
        // Range
        $range=array('start' => 0,
                     'count' => 1
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

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getNumbers');

        // Call function
        $result     = $this->SoapEngine->soapclient->getNumbers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {

            if ($result->total) {
                $number = array('number'   => $result->numbers[0]->id->number,
                                'tld'      => $result->numbers[0]->id->tld,
                                'mappings' => $result->numbers[0]->mappings
                                );

                return $number;
            }
        }

        return false;
    }

    function showSeachFormCustom()
    {

        /*
        print " <select name=range_filter><option>";
        $selected_range[$_REQUEST['range_filter']]='selected';
        foreach ($this->ranges as $_range) {
            $rangeId=$_range['prefix'].'@'.$_range['tld'];
            printf ("<option value='%s' %s>%s +%s",$rangeId,$selected_range[$rangeId],$_range['tld'],$_range['prefix']);
        }

        print "</select>";
        */

        printf (" <div class='input-prepend'><span class='add-on'>Number</span><input class=span2 type=text size=15 name=number_filter value='%s'></div>",$_REQUEST['number_filter']);

        printf (" <div class='input-prepend'><span class='add-on'><nobr>Map to</span>");
        print "<select class=span2 name=type_filter>
        <option>
        ";
        reset($this->NAPTR_services);
        $selected_naptr_service[$this->filters['type']]='selected';
        foreach ($this->NAPTR_services as $k => $v) {
            printf ("<option value='%s' %s>%s",$k,$selected_naptr_service[$k],$this->NAPTR_services[$k]['webname']);
        }

        print "
        </select>
        ";

        printf ("<input class=span3 type=text size=30 name=mapto_filter value='%s'></nobr></div>",$this->filters['mapto']);
        printf (" <div class='input-prepend'><span class='add-on'>Owner</span><input type=text size=7 class=span1 name=owner_filter value='%s'></div>",$this->filters['owner']);
    }

    function deleteRecord($dictionary=array())
    {
        if (!$dictionary['confirm'] && !$_REQUEST['confirm']) {
            print "<p><font color=red>Please press on Confirm to confirm the delete. </font>";
            return true;
        }

        if ($dictionary['number']) {
            $number=$dictionary['number'];
        } else {
            $number=$this->filters['number'];
        }

        if ($dictionary['tld']) {
            $tld=$dictionary['tld'];
        } else {
            $tld=$this->filters['tld'];
        }

        if ($dictionary['mapto']) {
            $mapto=$dictionary['mapto'];
        } else {
            $mapto=$this->filters['mapto'];
        }

        if (!strlen($number) || !strlen($tld)) {
            print "<p><font color=red>Error: missing ENUM number or TLD </font>";
            return false;
        }

        $enum_id=array('number' => $number,
                       'tld'    => $tld
                       );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getNumber');
        $result     = $this->SoapEngine->soapclient->getNumber($enum_id);

        if (!(new PEAR)->isError($result)) {
            // the number exists and we make an update
            $result_new=$result;

            if (count($result->mappings) > 1) {
                foreach ($result->mappings as $_mapping) {
                    if ($_mapping->mapto != $mapto) {
                        $mappings_new[]=array('type'     => $_mapping->type,
                                              'mapto'    => $_mapping->mapto,
                                              'ttl'      => $_mapping->ttl,
                                              'priority' => $_mapping->priority,
                                              'id'       => $_mapping->id
                                          );
                    }
                }

                if (!is_array($mappings_new)) $mappings_new = array();

                $result_new->mappings=$mappings_new;

                $function=array('commit'   => array('name'       => 'updateNumber',
                                                    'parameters' => array($result_new),
                                                    'logs'       => array('success' => sprintf('ENUM mapping %s has been deleted',$mapto)))
                                );

                $result = $this->SoapEngine->execute($function,$this->html);

                if ($this->checkLogSoapError($result, true)) {
                    return false;
                } else {
                    return true;
                }

            } else {
                $function=array('commit'   => array('name'       => 'deleteNumber',
                                                    'parameters' => array($enum_id),
                                                    'logs'       => array('success' => sprintf('ENUM number +%s under %s has been deleted',$number,$tld))),
                                );

                $result = $this->SoapEngine->execute($function,$this->html);

                if ($this->checkLogSoapError($result, true)) {
                    return false;
                } else {
                    return true;
                }
            }

            unset($this->filters);
        } else {
            return false;
        }
    }

    function showAddForm()
    {
        if ($this->selectionActive) return;

        //if ($this->adminonly && !$this->filters['reseller']) return;

        if (!count($this->ranges)) {
            //print "<p><font color=red>You must create at least one ENUM range before adding ENUM numbers</font>";
            return false;
        }

        printf ("<form class=form-inline method=post name=addform action=%s>",$_SERVER['PHP_SELF']);

        print "
        <div class='well well-small'>
        ";

        if ($this->adminonly) {
        	printf (" <input type=hidden name=reseller_filter value='%s'>",$this->filters['reseller']);
        }

        print "
        <input type=submit name=action class='btn btn-warning' value=Add>
        <div class='input-prepend'><span class='add-on'>";
        printf (" Number");

        print "</span><select class=span3 name=range>";

        if ($_REQUEST['range']) {
            $selected_range[$_REQUEST['range']]='selected';
        } else if ($_range=$this->getCustomerProperty('enum_numbers_last_range')) {
            $selected_range[$_range]='selected';
        }

        foreach ($this->ranges as $_range) {
            $rangeId=$_range['prefix'].'@'.$_range['tld'];
            printf ("<option value='%s' %s>+%s (%s)",$rangeId,$selected_range[$rangeId],$_range['prefix'],$_range['tld']);
        }

        print "</select>";

        if ($_REQUEST['number']) {
            printf ("<input class=span2 type=text name=number value='%s'>",$_REQUEST['number']);
        } else if ($_number=$this->getCustomerProperty('enum_numbers_last_number')) {
            $_prefix=$_range['prefix'];
            preg_match("/^$_prefix(.*)/",$_number,$m);
            printf ("<input class=span1 type=text name=number value='%s'>",$m[1]);
        } else {
            printf ("<input class=span1 type=text name=number>");
        }

        print "</div> <div class='input-prepend'><span class='add-on'>";

        printf ("Map to");
        print "</span><select class=span2 name=type>";

        if ($_REQUEST['type']) {
            $selected_naptr_service[$_REQUEST['type']]='selected';
        } else if ($_type=$this->getCustomerProperty('enum_numbers_last_type')) {
            $selected_naptr_service[$_type]='selected';
        }

        reset($this->NAPTR_services);

        foreach ($this->NAPTR_services as $k => $v) {
            printf ("<option value='%s' %s>%s",$k,$selected_naptr_service[$k],$this->NAPTR_services[$k]['webname']);
        }

        print "
        </select>
        ";
        if ($_REQUEST['type']) {
            $selected_naptr_service[$_REQUEST['type']]='selected';
        } else if ($_type=$this->getCustomerProperty('enum_numbers_last_type')) {
            $selected_naptr_service[$_type]='selected';
        }

        printf (" <input class=span2 type=text size=40 name=mapto value='%s'>",$_REQUEST['mapto']);

        print "</div> <div class='input-prepend'><span class='add-on'>";
        print "TTL";
        print "</span>";

        if ($_REQUEST['ttl']) {
            printf ("<input class=span1 type=text size=5 name=ttl value='%s'></div>",$_REQUEST['ttl']);
        } else if ($_ttl=$this->getCustomerProperty('enum_numbers_last_ttl')) {
            printf ("<input class=span1 type=text size=5 name=ttl value='%s'></div>",$_ttl);
        } else {
            printf ("<input class=span1 type=text size=5 name=ttl value='3600'></div>");
        }
        printf (" <div class='input-prepend'><span class='add-on'>Owner</span><input class=span1 type=text size=7 name=owner value='%s'></div>",$_REQUEST['owner']);
        printf (" <div class='input-prepend'><span class='add-on'>Info</span><input class=span1 type=text size=10 name=info value='%s'></div>",$_REQUEST['info']);

        $this->printHiddenFormElements();

        print "
            </div>
        </form>
        ";
    }

    function getAllowedDomains()
    {
        // Filter
        $filter=array('prefix'   => '',
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );
        // Range
        $range=array('start' => 0,
                     'count' => 200
                     );

        // Order
        $orderBy = array('attribute' => 'prefix',
                         'direction' => 'ASC'
                         );

        // Compose query
        $Query=array('filter'  => $filter,
                     'orderBy' => $orderBy,
                     'range'   => $range
                     );

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getRanges');
        $result     = $this->SoapEngine->soapclient->getRanges($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            foreach($result->ranges as $range) {
                $this->ranges[]=array('prefix'    => $range->id->prefix,
                                      'tld'       => $range->id->tld,
                                      'minDigits' => $range->minDigits,
                                      'maxDigits' => $range->maxDigits
                                      );
                if (in_array($range->id->tld,$this->allowedDomains)) continue;
                $this->allowedDomains[]=$range->id->tld;
                $seen[$range->id->tld]++;
            }
            if (!$seen[$this->SoapEngine->default_enum_tld]) {
                $this->allowedDomains[]=$this->SoapEngine->default_enum_tld;
            }
        }
    }

    function addRecord($dictionary=array()) {
        $prefix='';
        if ($dictionary['range']) {
            list($prefix,$tld)=explode('@',trim($dictionary['range']));
            $this->skipSaveProperties=true;
        } else if ($dictionary['tld']) {
            $tld = $dictionary['tld'];
        } else if ($_REQUEST['range']) {
            list($prefix,$tld)=explode('@',trim($_REQUEST['range']));
        } else {
            $tld = trim($_REQUEST['tld']);
        }

        if ($dictionary['number']) {
            $number = $dictionary['number'];
        } else {
            $number = trim($_REQUEST['number']);
        }

        $number=$prefix.$number;

        if (!strlen($tld)) {
        	$tld=$this->SoapEngine->default_enum_tld;
        }

        if (!strlen($tld) || !strlen($number) || !is_numeric($number)) {
            printf ("<p><font color=red>Error: Missing TLD or number. </font>");
            return false;
        }

        if ($dictionary['ttl']) {
            $ttl = intval($dictionary['ttl']);
        } else {
            $ttl = intval(trim($_REQUEST['ttl']));
        }

        if (!$ttl) $ttl=3600;

        if ($dictionary['priority']) {
            $priority = intval($dictionary['priority']);
        } else {
            $priority = intval(trim($_REQUEST['priority']));
        }

        if ($dictionary['owner']) {
            $owner = intval($dictionary['owner']);
        } else {
            $owner = intval(trim($_REQUEST['owner']));
        }

        if ($dictionary['info']) {
            $info = $dictionary['info'];
        } else {
            $info = trim($_REQUEST['info']);
        }

        if (!$priority) $priority=5;

        $enum_id=array('number' => $number,
                       'tld'    => $tld);

        if ($dictionary['mapto']) {
            $mapto = $dictionary['mapto'];
        } else {
            $mapto = trim($_REQUEST['mapto']);
        }

        if ($dictionary['type']) {
            $type = $dictionary['type'];
        } else {
            $type = trim($_REQUEST['type']);
        }

        if (preg_match("/^([a-z0-9]+:\/\/)(.*)$/i",$mapto,$m)) {
            $_scheme = $m[1];
            $_value  = $m[2];
        } else if (preg_match("/^([a-z0-9]+:)(.*)$/i",$mapto,$m)) {
            $_scheme = $m[1];
            $_value  = $m[2];
        } else {
            $_scheme = '';
            $_value  = $mapto;
        }

        if (!$_value) {
            $lastNumber=$this->getLastNumber();
            foreach($lastNumber['mappings'] as $_mapping) {
                if ($_mapping->type == trim($type)) {
                    if (preg_match("/^(.*)@(.*)$/",$_mapping->mapto,$m)) {
                        $_value = $number.'@'.$m[2];
                        break;
                    }
                }
            }
        }

        if (!$_scheme || !in_array($_scheme,$this->NAPTR_services[trim($type)]['schemas'])) {
            $_scheme=$this->NAPTR_services[trim($type)]['schemas'][0];
        }

        $mapto=$_scheme.$_value;

        $enum_number=array('id'       => $enum_id,
                           'owner'    => $owner,
                           'info'     => $info,
                           'mappings' => array(array('type'     => $type,
                                                     'mapto'    => $mapto,
                                                     'ttl'      => $ttl,
                                                     'priority' => $priority
                                                    )
                                               )
                           );

        if (!$this->skipSaveProperties=true) {

            $_p=array(
                      array('name'       => 'enum_numbers_last_range',
                            'category'   => 'web',
                            'value'      => $_REQUEST['range'],
                            'permission' => 'customer'
                           ),
                      array('name'       => 'enum_numbers_last_type',
                            'category'   => 'web',
                            'value'      => "$type",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'enum_numbers_last_number',
                            'category'   => 'web',
                            'value'      => "$number",
                            'permission' => 'customer'
                           ),
                      array('name'       => 'enum_numbers_last_ttl',
                            'category'   => 'web',
                            'value'      => "$ttl",
                            'permission' => 'customer'
                           )
                      );

            $this->setCustomerProperties($_p);
        }

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getNumber');
        $result     = $this->SoapEngine->soapclient->getNumber($enum_id);

        if ((new PEAR)->isError($result)) {
            $error_msg=$result->getMessage();
            $error_fault=$result->getFault();
            $error_code=$result->getCode();

            if ($error_fault->detail->exception->errorcode == "3002") {

                $function=array('commit'   => array('name'       => 'addNumber',
                                                    'parameters' => array($enum_number),
                                                    'logs'       => array('success' => sprintf('ENUM number +%s under %s has been added',$number,$tld)))
                                );

                $result = $this->SoapEngine->execute($function,$this->html);

                if ($this->checkLogSoapError($result, true)) {
                    return false;
                } else {
                   return true;
                }

            } else {
                $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                syslog(LOG_NOTICE, $log);
                return false;
            }
        } else {
            // the number exists and we make an update
            $result_new=$result;
            foreach ($result->mappings as $_mapping) {
                $mappings_new[]=array('type'     => $_mapping->type,
                                      'mapto'    => $_mapping->mapto,
                                      'ttl'      => $_mapping->ttl,
                                      'priority' => $_mapping->priority,
                                      'id'       => $_mapping->id
                                      );

                if ($_mapping->mapto == $mapto) {
                    printf ("<p><font color=blue>Info: ENUM mapping %s for number %s already exists</font>",$mapto,$number);
                    return $result;
                }
            }

            $mappings_new[]=array('type'    => trim($type),
                                  'mapto'   => $mapto,
                                  'ttl'     => intval(trim($_REQUEST['ttl'])),
                                  'priority'=> intval(trim($_REQUEST['priority'])),
                                 );
            // add mapping
            $result_new->mappings=$mappings_new;

            $function=array('commit'   => array('name'       => 'updateNumber',
                                                'parameters' => array($result_new),
                                                'logs'       => array('success' => sprintf('ENUM number +%s under %s has been updated',$number,$tld)))
                            );

            $result = $this->SoapEngine->execute($function,$this->html);

            if ($this->checkLogSoapError($result, true)) {
                return false;
            } else {
                return true;
            }
        }
    }

    function getRecordKeys()
    {
        // Filter
        $filter=array('number'   => $this->filters['number'],
                      'tld'      => $this->filters['tld'],
                      'type'     => $this->filters['type'],
                      'mapto'    => $this->filters['mapto'],
                      'owner'    => intval($this->filters['owner']),
                      'customer' => intval($this->filters['customer']),
                      'reseller' => intval($this->filters['reseller'])
                      );
        // Range
        $range=array('start' => 0,
                     'count' => 1000
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

        // Insert credetials
        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getNumberss');

        // Call function
        $result     = $this->SoapEngine->soapclient->getNumbers($Query);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            foreach ($result->numbers as $number) {
                $this->selectionKeys[]=array('number' => $number->id->number,
                                             'tld'    => $number->id->tld);
            }
            return true;
        }
    }

    function showRecord($number) {

        print "<table border=0>";
        print "<tr>";
        print "<td>";
        print "<h3>Number</h3>";
        print "</td><td>";
        print "<h3>Mappings</h3>";
        print "</td>";
        print "</tr>";

        print "<tr>";
        print "<td valign=top>";

        print "<table border=0>";

        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        printf ("<tr><td class=border>DNS name</td><td class=border>%s</td></td>",
        $this->tel2enum($number->id->number,$number->id->tld));

        foreach (array_keys($this->Fields) as $item) {
            if ($this->Fields[$item]['name']) {
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><textarea cols=30 name=%s_form rows=4>%s</textarea></td>
                </tr>",
                $item_name,
                $item,
                $number->$item
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                </tr>",
                $item_name,
                $item,
                $number->$item
                );
            }
        }

        printf ("<input type=hidden name=tld_filter value='%s'>",$number->id->tld);
        printf ("<input type=hidden name=number_filter value='%s'>",$number->id->number);

        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "
        </table>
        ";

        print "</td><td valign=top>";

        print "<table border=0>";
        print "<tr>";
        print "<td></td>";
        print "<td class=border>Id</td>";
        print "<td class=border>Type</td>";
        print "<td class=border>Map to</td>";
        print "<td class=border>TTL</td>";
        print "</tr>";

        foreach ($number->mappings as $_mapping) {
            $j++;
            unset($selected_type);
            print "<tr>";
            print "<td>$j</td>";
            printf ("<td class=border>%d<input type=hidden name=mapping_id[] value='%d'></td>",$_mapping->id,$_mapping->id);
            $selected_type[$_mapping->type]='selected';
            printf ("
            <td class=border><select class=span2 name=mapping_type[]>");
            foreach ($this->NAPTR_services as $k => $v) {
                printf ("<option value='%s' %s>%s",$k,$selected_type[$k],$this->NAPTR_services[$k]['webname']);
            }

            print "
            </select>
            </td>";

            printf ("
            <td><input class=span4 name=mapping_mapto[] size=40 value='%s'></td>
            <td><input class=span1 name=mapping_ttl[] size=6 value='%s'></td>
            ",
            $_mapping->mapto,
            $_mapping->ttl
            );
            print "</tr>";
        }

        $j++;
        print "<tr>";
        print "<td></td>";
        print "<td></td>";

        printf ("
        <td class=border><select class=span2 name=mapping_type[]>");
        foreach ($this->NAPTR_services as $k => $v) {
            printf ("<option value='%s'>%s",$k,$this->NAPTR_services[$k]['webname']);
        }

        print "
        </select>
        </td>";

        printf ("
        <td class=border><input class=span4 name=mapping_mapto[] size=40></td>
        <td class=border><input class=span1 name=mapping_ttl[] size=6></td>
        "
        );

        print "</tr>";

        print "</table>";

        print "</td>";
        print "</tr>";

        print "
        <tr>
        <td>
        <input type=submit value=Update>
        </td>
        </tr>
        ";
        print "</form>";
        print "</table>";

    }

    function getRecord($enumid) {

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getNumber');
        $result     = $this->SoapEngine->soapclient->getNumber($enumid);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            return $result;
        }
    }

    function updateRecord () {
        //print "<p>Updating number ...";

        if (!$_REQUEST['number_filter'] || !$_REQUEST['tld_filter']) return;

        $enumid=array('number' => $_REQUEST['number_filter'],
                      'tld'    => $_REQUEST['tld_filter']
                     );

        if (!$number = $this->getRecord($enumid)) {
            return false;
        }

        $number_old=$number;

        $new_mappings=array();

        /*
        foreach ($number->mappings as $_mapping) {
            foreach (array_keys($this->mapping_fields) as $field) {
                if ($this->mapping_fields[$field] == 'integer') {
                    $new_mapping[$field]=intval($_mapping->$field);
                } else {
                    $new_mapping[$field]=$_mapping->$field;
                }
            }

            $new_mappings[]=$new_mapping;
        }
        */

        $j=0;
        while ($j< count($_REQUEST['mapping_type'])) {
            $mapto    = $_REQUEST['mapping_mapto'][$j];
            $type     = $_REQUEST['mapping_type'][$j];
            $id       = $_REQUEST['mapping_id'][$j];
            $ttl      = intval($_REQUEST['mapping_ttl'][$j]);
            $priority = intval($_REQUEST['mapping_priority'][$j]);

            if (!$ttl) $ttl = $this->default_ttl;
            if (!$priority) $priority = $this->default_priority;

            if (strlen($mapto)) {
                if (preg_match("/^([a-z0-9]+:\/\/)(.*)$/i",$mapto,$m)) {
                    $_scheme = $m[1];
                    $_value  = $m[2];
                } else if (preg_match("/^([a-z0-9]+:)(.*)$/i",$mapto,$m)) {
                    $_scheme = $m[1];
                    $_value  = $m[2];
                } else {
                    $_scheme = '';
                    $_value  = $mapto;
                }

                reset($this->NAPTR_services);
                if (!$_scheme || !in_array($_scheme,$this->NAPTR_services[trim($type)]['schemas'])) {
                    $_scheme=$this->NAPTR_services[trim($type)]['schemas'][0];
                }

                $mapto=$_scheme.$_value;

                $new_mappings[]=array( 'type'     => $type,
                                       'ttl'      => $ttl,
                                       'id'       => intval($id),
                                       'mapto'    => $mapto,
                                       'priority' => $priority
                                       );
            }

            $j++;
        }

        $number->mappings=$new_mappings;

        if (!is_array($number->mappings)) $number->mappings=array();

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name,$_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $number->$item = intval($_REQUEST[$var_name]);
            } else {
                $number->$item = trim($_REQUEST[$var_name]);
            }
        }

        //print_r($number);
        $function=array('commit'   => array('name'       => 'updateNumber',
                                            'parameters' => array($number),
                                            'logs'       => array('success' => sprintf('ENUM number +%s under %s has been updated',$enumid['number'],$enumid['tld'])))
                        );

        $result = $this->SoapEngine->execute($function,$this->html);

        dprint_r($result)    ;
        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            return true;
        }
    }

    function showTextBeforeCustomerSelection() {
        print _("Range owner");
    }

}

