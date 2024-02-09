<?php

class EnumRanges extends Records
{
    var $selectionActiveExceptions=array('tld');
	var $record_generator='';

    // only admin can add prefixes below
    var $deniedPrefixes=array('1','20','210','211','212','213','214','215','216','217','218','219','220','221','222','223','224','225','226','227','228','229','230','231','232','233','234','235','236','237','238','239','240','241','242','243','244','245','246','247','248','249','250','251','252','253','254','255','256','257','258','259','260','261','262','263','264','265','266','267','268','269','27','280','281','282','283','284','285','286','287','288','289','290','291','292','293','294','295','296','297','298','299','30','31','32','33','34','350','351','352','353','354','355','356','357','358','359','36','370','371','372','373','374','375','376','377','378','379','380','381','382','383','384','385','386','387','388','389','39','40','41','420','421','422','423','424','425','426','427','428','429','43','44','45','46','47','48','49','500','501','502','503','504','505','506','507','508','509','51','52','53','54','55','56','57','58','590','591','592','593','594','595','596','597','598','599','60','61','62','63','64','65','66','670','671','672','673','674','675','676','677','678','679','680','681','682','683','684','685','686','687','688','689','690','691','692','693','694','695','696','697','698','699','7','800','801','802','803','804','805','806','807','808','809','81','82','830','831','832','833','834','835','836','837','838','839','84','850','851','852','853','854','855','856','857','858','859','86','870','871','872','873','874','875','876','877','878','879','880','881','882','883','884','885','886','887','888','889','890','891','892','893','894','895','896','897','898','899','90','91','92','93','94','95','960','961','962','963','964','965','966','967','968','969','970','971','972','973','974','975','976','977','978','979','98','990','991','992','993','994','995','996','997','998','999');

    var $FieldsAdminOnly=array(
                              'reseller' => array('type'=>'integer',
                                                   'help' => 'Range owner')
                              );

    var $Fields=array(
                              'customer'    => array('type'=>'integer',
                                                   'help' => 'Range owner'
                                                      ),
                              'serial'        => array('type'=>'integer',
                                                     'help'=>'DNS serial number',
                                                     'readonly' => 1
                                                     ),
                              'ttl'         => array('type'=>'integer',
                                                     'help'=>'Cache period in DNS clients'
                                                     ),
                              'info'        => array('type'=>'string',
                                                     'help' =>'Range description'
                                                     ),
                              'size'        => array('type'=>'integer',
                                                     'help'=>'Maximum number of telephone numbers'
                                                     ),
                              'minDigits'         => array('type'=>'integer',
                                                           'help'=>'Minimum number of digits for telephone numbers'
                                                           ),
                              'maxDigits'         => array('type'=>'integer',
                                                           'help'=>'Maximum number of digits for telephone numbers'
                                                              )
                              );

    public function __construct($SoapEngine)
    {
        dprint("init EnumRanges");

        $this->filters   = array('prefix'       => trim(ltrim($_REQUEST['prefix_filter']),'+'),
                                 'tld'          => trim($_REQUEST['tld_filter']),
                                 'info'         => trim($_REQUEST['info_filter'])
                                 );

        parent::__construct($SoapEngine);

        $this->sortElements=array('changeDate' => 'Change date',
                                    'prefix'     => 'Prefix',
                                  'tld'        => 'TLD'
                                 );
        /*
        $this->Fields['nameservers'] = array('type'=>'text',
                                              'name'=>'Name servers',
                                              'help'=>'Name servers authoritative for this DNS zone'
                                              );
        */

        if ($this->login_credentials['reseller_filters'][$this->reseller]['record_generator']) {
            //printf ("Engine: %s",$this->SoapEngine->soapEngine);
            if (is_array($this->login_credentials['reseller_filters'][$this->reseller]['record_generator'])) {
                $_rg=$this->login_credentials['reseller_filters'][$this->reseller]['record_generator'];
                if ($_rg[$this->SoapEngine->soapEngine]) {
					$this->record_generator=$_rg[$this->SoapEngine->soapEngine];
                }
            } else {
				$this->record_generator=$this->login_credentials['reseller_filters'][$this->reseller]['record_generator'];
            }
        } else  if (strlen($this->SoapEngine->record_generator)) {
            $this->record_generator=$this->SoapEngine->record_generator;
        }
    }

    function listRecords()
    {
        $this->getAllowedDomains();
        $this->showSeachForm();

        // Filter
        $filter=array('prefix'   => $this->filters['prefix'],
                      'tld'      => $this->filters['tld'],
                      'info'     => $this->filters['info'],
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

        $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
        $this->log_action('getRanges');
        $result     = $this->SoapEngine->soapclient->getRanges($Query);

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
            <table class='table table-striped table-condensed' width=100%>
            <thead>
            <tr>
            <th>Id</th>
            <th>Owner</th>
            <th>Prefix </th>
            <th>TLD</th>
            <th>Serial</th>
            <th>TTL</th>
            <th>Info</th>
            <th>Min</th>
            <th>Max</th>
            <th>Size</th>
            <th colspan=2>Used</th>
            <th>Change date</th>
            <th>Actions</th>
            </tr></thead>
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

                    if (!$result->ranges[$i]) break;
                    $range = $result->ranges[$i];

                    $index=$this->next+$i+1;

                    $_url = $this->url.sprintf("&service=%s&action=Delete&prefix_filter=%s&tld_filter=%s",
                    urlencode($this->SoapEngine->service),
                    urlencode($range->id->prefix),
                    urlencode($range->id->tld)
                    );

                    if ($this->adminonly) $_url.= sprintf ("&reseller_filter=%s",$range->reseller);

                    if ($_REQUEST['action'] == 'Delete' &&
                        $_REQUEST['prefix_filter'] == $range->id->prefix &&
                        $_REQUEST['tld_filter'] == $range->id->tld) {
                        $_url .= "&confirm=1";
                        $actionText = "<font color=red>Confirm</font>";
                    } else {
                        $actionText = "Delete";
                    }

                    if ($this->adminonly) {
                        $range_url=sprintf('<a href=%s&service=%s&reseller_filter=%s&prefix_filter=%s&tld_filter=%s>%s</a>',$this->url, $this->SoapEngine->service, $range->reseller, $range->id->prefix, $range->id->tld, $range->id->prefix);
                    } else {
                        $range_url=sprintf('<a href=%s&&service=%s&prefix_filter=%s&tld_filter=%s>%s</a>',$this->url, $this->SoapEngine->service, $range->id->prefix, $range->id->tld, $range->id->prefix);
                    }

                    if ($this->record_generator) {
                        $generator_url=sprintf('<a class="btn-small btn-primary" href=%s&generatorId=%s&range=%s@%s&number_length=%s&reseller_filter=%s target=generator>+Numbers</a>',$this->url, $this->record_generator, $range->id->prefix, $range->id->tld, $range->maxDigits, $range->reseller);
                    } else {
                        $generator_url='';
                    }

                    if ($range->size) {
                        $usage=intval(100*$range->used/$range->size);
                        $bar=$this->makebar($usage);
                    } else {
                        $bar="";
                    }

                    $_customer_url = $this->url.sprintf("&service=customers@%s&customer_filter=%s",
                    urlencode($this->SoapEngine->customer_engine),
                    urlencode($range->customer)
                    );

                    $_nameservers='';
                    foreach ($range->nameservers as $_ns) {
                        $_nameservers.= $_ns.' ';
                    }
                    printf("
                        <tr>
                            <td>%s</td>
                            <td><a href=%s>%s.%s</a></td>
                            <td>+%s</td>
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
                            <td><a class='btn-small btn-danger' href=%s>%s</a>%s</td>
                        </tr>",
                        $index,
                        $_customer_url,
                        $range->customer,
                        $range->reseller,
                        $range_url,
                        $range->id->tld,
                        $range->serial,
                        $range->ttl,
                        $range->info,
                        $range->minDigits,
                        $range->maxDigits,
                        $range->size,
                        $range->used,
                        $bar,
                        $range->changeDate,
                        $_url,
                        $actionText,
                        $generator_url
                    );
                    $i++;
                }
            }

            print "</table>";

            if ($this->rows == 1) {
                $this->showRecord($range);
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

        if (!strlen($this->filters['prefix']) || !strlen($this->filters['tld'])) {
            print "<p><font color=red>Error: missing ENUM range id </font>";
            return false;
        }

        $rangeId=array('prefix'=>$this->filters['prefix'],
                       'tld'=>$this->filters['tld']);

        $function=array('commit'   => array('name'       => 'deleteRange',
                                            'parameters' => array($rangeId),
                                            'logs'       => array('success' => sprintf('ENUM range +%s under %s has been deleted',$this->filters['prefix'], $this->filters['tld'])
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

        printf ("<form class='form-inline' method=post name=addform action=%s>",$_SERVER['PHP_SELF']);

        print "
        <div class='well well-small'>
        ";

        print "
        <input type=submit class='btn btn-warning' name=action value=Add>
        ";
        $this->showCustomerTextBox();

        printf ("</div> <div class='input-prepend'><span class='add-on'>Prefix +</span><input type=text class=input-medium size=15 name=prefix value='%s'></div>",$_REQUEST['prefix']);
        printf (" <div class='input-prepend'><span class='add-on'>TLD</span>");

        if ($_REQUEST['tld']) {
            printf ("<input class=span2 type=text size=15 name=tld value='%s'></div>",$_REQUEST['tld']);
        } else if ($this->filters['tld']) {
            printf ("<input class=span2 type=text size=15 name=tld value='%s'></div>",$this->filters['tld']);
        } else if ($_tld=$this->getCustomerProperty('enum_ranges_last_tld')) {
            printf ("<input class=span2 type=text size=15 name=tld value='%s'></div>",$_tld);
        } else {
            printf ("<input class=span2 type=text size=15 name=tld></div>");
        }

        printf (" <div class='input-prepend'><span class='add-on'>TTL</span><input class=span1 type=text size=5 name=ttl value=3600></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Min Digits</span><input class=span1 type=text size=3 name=minDigits value=11></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Max Digits</span><input class=span1 type=text size=3 name=maxDigits value=11></div>");
        printf (" <div class='input-prepend'><span class='add-on'>Info</span><input type=text size=15 name=info class=span2 value='%s'></div>",$_REQUEST['info']);

        $this->printHiddenFormElements();

        print "
        </div>
        </form>
        ";
    }

    function addRecord($dictionary=array())
    {
        $tld    = trim($_REQUEST['tld']);
        $prefix = trim($_REQUEST['prefix']);
        $size   = trim($_REQUEST['size']);
        $info   = trim($_REQUEST['info']);

        if (!strlen($tld)) {
            $tld=$this->SoapEngine->default_enum_tld;
        }

        if (!strlen($tld) || !strlen($prefix) || !is_numeric($prefix)) {
            printf("<p><font color=red>Error: Missing TLD or prefix. </font>");
            return false;
        }

        if (!$this->adminonly) {
            if (in_array($prefix, $this->deniedPrefixes)) {
                print "<p><font color=red>Error: Only an administrator account can create the prefix coresponding to a country code.</font>";
                return false;
            }
        }

        list($customer, $reseller)=$this->customerFromLogin($dictionary);

        if (!trim($_REQUEST['ttl'])) {
            $ttl = 3600;
        } else {
            $ttl = intval(trim($_REQUEST['ttl']));
        }

        $range = array(
            'id'         => array(
                'prefix' => $prefix,
                'tld'    => $tld
            ),
            'ttl'        => $ttl,
            'info'       => $info,
            'minDigits'  => intval(trim($_REQUEST['minDigits'])),
            'maxDigits'  => intval(trim($_REQUEST['maxDigits'])),
            'size'       => intval($size),
            'customer'   => intval($customer),
            'reseller'   => intval($reseller)
        );

        $deleteRange = array(
            'prefix'=>$prefix,
            'tld'=>$tld
        );

        $_p = array(
            array(
                'name'       => 'enum_ranges_last_tld',
                'category'   => 'web',
                'value'      => "$tld",
                'permission' => 'customer'
            )
        );

        $this->setCustomerProperties($_p);

        $function = array(
            'commit'   => array(
                'name'       => 'addRange',
                'parameters' => array($range),
                'logs'       => array('success' => sprintf('ENUM range +%s under %s has been added',$prefix, $tld))
            )
        );

        $result = $this->SoapEngine->execute($function, $this->html);

        dprint_r($result);
        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            return true;
        }
    }

    function showSeachFormCustom()
    {
        printf (" <div class='input-prepend'><span class='add-on'>Prefix</span><input class=span2 type=text size=15 name=prefix_filter value='%s'></div>",$this->filters['prefix']);
        printf (" <div class='input-prepend'><span class='add-on'>TLD</span>");

        if (count($this->allowedDomains) > 0) {
            $selected_tld[$this->filters['tld']]='selected';
            printf ("<select class=span2 name=tld_filter>
            <option>");

            foreach ($this->allowedDomains as $_tld) {
                printf ("<option value='%s' %s>%s",$_tld, $selected_tld[$_tld], $_tld);
            }

            printf ("</select></div>");
        } else {
            printf ("<input class=span2 type=text size=20 name=tld_filter value='%s'></div>",$this->filters['tld']);
        }
        printf (" <div class='input-prepend'><span class='add-on'>Info</span><input class=span2 type=text size=10 name=info_filter value='%s'></div>",$this->filters['info']);
    }

    function getAllowedDomains()
    {
        // Filter
        $filter=array('prefix'   => '');
        // Range
        $range=array('start' => 0,
                     'count' => 500
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
                if (in_array($range->id->tld, $this->allowedDomains)) continue;
                $this->allowedDomains[]=$range->id->tld;
                $seen[$range->id->tld]++;
            }

            if (!$seen[$this->SoapEngine->default_enum_tld]) {
                $this->allowedDomains[]=$this->SoapEngine->default_enum_tld;
            }
        }
    }

    function showRecord($range)
    {
        print "<table border=0 cellpadding=10>";
        print "
        <tr>
        <td valign=top>
        <table border=0>";
        printf ("<form method=post name=addform action=%s>",$_SERVER['PHP_SELF']);
        print "<input type=hidden name=action value=Update>";

        print "<tr>
        <td colspan=2><input type=submit value=Update>
        </td></tr>";

        printf ("<tr><td class=border>DNS zone</td><td class=border>%s</td></td>",
        $this->tel2enum($range->id->prefix, $range->id->tld));

        if ($this->adminonly) {

            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                if ($item == 'nameservers') {
                    foreach ($range->$item as $_item) {
                        $nameservers.=$_item."\n";
                    }
                    $item_value=$nameservers;
                } else {
                    $item_value=$range->$item;
                }

                if ($this->FieldsAdminOnly[$item]['name']) {
                    $item_name=$this->FieldsAdminOnly[$item]['name'];
                } else {
                    $item_name=ucfirst($item);
                }

                if ($this->FieldsAdminOnly[$item]['type'] == 'text') {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                    <td class=border valign=top>%s</td>
                    </tr>",
                    $item_name,
                    $item,
                    $item_value,
                    $this->FieldsAdminOnly[$item]['help']
                    );
                } else {
                    printf ("<tr>
                    <td class=border valign=top>%s</td>
                    <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                    <td class=border>%s</td>
                    </tr>",
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
                $item_name=$this->Fields[$item]['name'];
            } else {
                $item_name=ucfirst($item);
            }

            if ($item == 'nameservers') {
                foreach ($range->$item as $_item) {
                    $nameservers.=$_item."\n";
                }
                $item_value=$nameservers;
            } else {
                $item_value=$range->$item;
            }

            if ($this->Fields[$item]['type'] == 'text') {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><textarea cols=30 name=%s_form rows=7>%s</textarea></td>
                <td class=border valign=top>%s</td>
                </tr>",
                $item_name,
                $item,
                $item_value,
                $this->Fields[$item]['help']
                );
            }else if ($this->Fields[$item]['readonly']) {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border>%s</td>
                <td class=border valign=top>%s</td>
                </tr>",
                $item_name,
                $item_value,
                $this->Fields[$item]['help']
                );
            } else {
                printf ("<tr>
                <td class=border valign=top>%s</td>
                <td class=border><input name=%s_form size=30 type=text value='%s'></td>
                <td class=border>%s</td>
                </tr>",
                $item_name,
                $item,
                $item_value,
                $this->Fields[$item]['help']
                );
            }
        }

        printf ("<input type=hidden name=tld_filter value='%s'>",$range->id->tld);
        printf ("<input type=hidden name=prefix_filter value='%s'>",$range->id->prefix);
        $this->printFiltersToForm();
        $this->printHiddenFormElements();

        print "</form>";
        print "
        </table>
        ";
    }

    function updateRecord()
    {
        //print "<p>Updating range ...";

        if (!$_REQUEST['prefix_filter'] || !$_REQUEST['tld_filter']) return;

        $rangeid=array('prefix' => $_REQUEST['prefix_filter'],
                      'tld'    => $_REQUEST['tld_filter']
                     );

        if (!$range = $this->getRecord($rangeid)) {
            return false;
        }

        $range_old=$range;

        foreach (array_keys($this->Fields) as $item) {
            $var_name=$item.'_form';
            //printf ("<br>%s=%s",$var_name, $_REQUEST[$var_name]);
            if ($this->Fields[$item]['type'] == 'integer') {
                $range->$item = intval($_REQUEST[$var_name]);
            } else if ($item == 'nameservers') {
                $_txt=trim($_REQUEST[$var_name]);
                if (!strlen($_txt)) {
                    unset($range->$item);
                } else {
                    $_nameservers=array();
                    $_lines=explode("\n",$_txt);
                    foreach ($_lines as $_line) {
                        $_ns=trim($_line);
                        $_nameservers[]=$_ns;
                    }
                    $range->$item=$_nameservers;
                }
            } else {
                $range->$item = trim($_REQUEST[$var_name]);
            }
        }

        if ($this->adminonly) {
            foreach (array_keys($this->FieldsAdminOnly) as $item) {
                $var_name=$item.'_form';
                if ($this->FieldsAdminOnly[$item]['type'] == 'integer') {
                    $range->$item = intval($_REQUEST[$var_name]);
                } else {
                    $range->$item = trim($_REQUEST[$var_name]);
                }
            }
        }

        $function=array('commit'   => array('name'       => 'updateRange',
                                            'parameters' => array($range),
                                            'logs'       => array('success' => sprintf('ENUM range +%s under %s has been updated',$rangeid['prefix'], $rangeid['tld'])))
                        );

        $result = $this->SoapEngine->execute($function, $this->html);
        dprint_r($result);

        if ($this->checkLogSoapError($result, true)) {
            return false;
        } else {
            return true;
        }
    }

    function getRecord($rangeid)
    {
        // Filter
        if (!$rangeid['prefix'] || !$rangeid['tld']) {
            print "Error in getRecord(): Missing prefix or tld";
            return false;
        }

        $filter=array('prefix'   => $rangeid['prefix'],
                      'tld'      => $rangeid['tld']
                      );

        // Range
        $range=array('start' => 0,
                     'count' => 1
                     );

        // Order
        $orderBy = array('attribute' => 'changeDate',
                         'direction' => 'DESC'
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
            if ($result->ranges[0]){
                return $result->ranges[0];
            } else {
                return false;
            }
        }
    }
}
