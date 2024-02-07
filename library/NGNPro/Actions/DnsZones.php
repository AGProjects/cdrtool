<?php

class DnsZonesActions extends Actions {
	var $sub_action_parameter_size = 50;

    var $actions=array(
                       'changettl'      => 'Change TTL to:',
                       'changeexpire'   => 'Change Expire to:',
                       'changeminimum'  => 'Change Minimum to:',
                       'changeretry'    => 'Change Retry to:',
                       'changeinfo'     => 'Change Info to:',
                       'addnsrecord'    => 'Add name server:',
                       'removensrecord' => 'Remove name server:',
                       'delete'         => 'Delete zones',
                       'export'         => 'Export zones'
                       );

    public function execute($selectionKeys, $action, $sub_action_parameter)
    {
        if (!in_array($action, array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        if ($action!='export')  {
            print "<ol>";
        } else {
            $exported_data=array('dns_zones'=>array());
            $export_customers=array();
        }

        foreach($selectionKeys as $key) {
            flush();
            if ($action!='export')  {
                print "<li>";
            }
            //printf ("Performing action=%s on key=%s",$action,$key['name']);

            if ($action=='delete') {
                $this->log_action('deleteZone');
                $function=array('commit'   => array('name'       => 'deleteZone',
                                                    'parameters' => array($key['name']),
                                                    'logs'       => array('success' => sprintf('Zone %s has been deleted',$key['name'])
                                                                          )
                                                   )

                                );
                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='export') {
                // Filter
                $filter=array(
                              'zone'     => $key['name']
                              );

                $range   = array('start' => 0,'count' => 5000);
                // Compose query
                $Query=array('filter'  => $filter,
                             'range'   => $range
                             );

                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $result     = $this->SoapEngine->soapclient->getZone($key['name']);
                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    syslog(LOG_NOTICE, $log);
                    return false;
                } else {
                    if (!in_array($result->customer, $export_customers)) {
                        $export_customers[]=$result->customer;
                    }
                    if (!in_array($result->reseller, $export_customers)) {
                        $export_customers[]=$result->reseller;
                    }
                    $exported_data['dns_zones'][] = objectToArray($result);
                }

                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getRecords');
                // Call function
                $result = call_user_func_array(array($this->SoapEngine->soapclient,'getRecords'),array($Query));

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    syslog(LOG_NOTICE, $log);
                    return false;
                } else {
                    $exported_data['dns_records'] = objectToArray($result->records);
                }
            } else if ($action  == 'changettl') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if ((new PEAR)->isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: TTL '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->ttl=intval($sub_action_parameter);
                    $this->log_action('updateZone');
                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('TTL for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeexpire') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if ((new PEAR)->isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: Expire '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->expire=intval($sub_action_parameter);
                    $this->log_action('updateZone');

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Expire for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeminimum') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if ((new PEAR)->isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: Minimum '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->minimum=intval($sub_action_parameter);
                    $this->log_action('updateZone');

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Minimum for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'addnsrecord') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if ((new PEAR)->isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

					$zone->nameservers[]=$sub_action_parameter;
					$zone->nameservers=array_unique($zone->nameservers);
                    $this->log_action('updateZone');

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Added NS record %s for zone %s',$sub_action_parameter,$key['name'])
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'removensrecord') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if ((new PEAR)->isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
					$new_servers=array();
					foreach ($zone->nameservers as $_ns) {
                        if ($_ns == $sub_action_parameter) continue;
                        $new_servers[]=$_ns;
                    }

					$zone->nameservers=array_unique($new_servers);
                    $this->log_action('updateZone');

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('NS record %s removed from zone %s',$sub_action_parameter,$key['name'])
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeretry') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if ((new PEAR)->isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: Retry '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $zone->retry=intval($sub_action_parameter);
                    $this->log_action('updateZone');

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Retry for zone %s has been set to %d',$key['name'],intval($sub_action_parameter))
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeinfo') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getZone');
                $zone     = $this->SoapEngine->soapclient->getZone($key['name']);

                if ((new PEAR)->isError($zone)) {
                    $error_msg  = $zone->getMessage();
                    $error_fault= $zone->getFault();
                    $error_code = $zone->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                	$zone->info=$sub_action_parameter;
                    $this->log_action('updateZone');

                    $function=array('commit'   => array('name'       => 'updateZone',
                                                        'parameters' => array($zone),
                                                        'logs'       => array('success' => sprintf('Info for zone %s has been set to %s',$key['name'],$sub_action_parameter)
                                                                              )
                                                       )
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            }
        }

        if ($action!='export')  {
            print "</ol>";
        } else {
            // Filter
            foreach ($export_customers as $customer) {
                $filter=array(
                              'customer'     => intval($customer),
                              );

                // Compose query
                $Query=array('filter'     => $filter
                                );

                // Insert credetials
                $this->SoapEngine->soapclientCustomers->addHeader($this->SoapEngine->SoapAuth);
                $this->getCustomers('getZone');

                // Call function
                $result     = $this->SoapEngine->soapclientCustomers->getCustomers($Query);
                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    $log=sprintf("SOAP request error from %s: %s (%s): %s",$this->SoapEngine->SOAPurl,$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    syslog(LOG_NOTICE, $log);
                    return false;
                } else {
                    $exported_data['customers'] = objectToArray($result->accounts);
                }
            }
            print_r(json_encode($exported_data));
        }
    }
}
