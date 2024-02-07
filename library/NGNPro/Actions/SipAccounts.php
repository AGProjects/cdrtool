<?php

class SipAccountsActions extends Actions {
    var $actions=array('block'          => 'Block SIP accounts',
                       'deblock'        => 'Deblock SIP accounts',
                       'enable_pstn'    => 'Enable access to PSTN for the SIP accounts',
                       'disable_pstn'   => 'Disable access to PSTN for the SIP accounts',
                       'deblock_quota'  => 'Deblock SIP accounts blocked by quota',
                       'prepaid'        => 'Make SIP accounts prepaid',
                       'postpaid'       => 'Make SIP accounts postpaid',
                       'delete'         => 'Delete SIP accounts',
                       'setquota'       => 'Set quota of SIP account to:',
                       'rpidasusername' => 'Set PSTN caller ID as the username',
                       'prefixtorpid'   => 'Add to PSTN caller ID this prefix:',
                       'rmdsfromrpid'   => 'Remove from PSTN caller ID digits:',
                       'addtogroup'     => 'Add SIP accounts to group:',
                       'removefromgroup'=> 'Remove SIP accounts from group:',
                       'addbalance'     => 'Add to prepaid balance value:',
                       'changeowner'    => 'Change owner to:',
                       'changefirstname'=> 'Change first name to:',
                       'changelastname' => 'Change last name to:',
                       'changepassword' => 'Change password to:'
                       );

    public function execute($selectionKeys, $action, $sub_action_parameter)
    {
        if (!in_array($action, array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";
        foreach($selectionKeys as $key) {

            flush();
            //printf ("Performing action=%s on key=%s",$action,$key);

            $account=array('username' => $key['username'],
                           'domain'   => $key['domain']
                          );

            printf ("<li>%s@%s",$key['username'],$key['domain']);

            if ($action=='block') {
                $this->log_action('addToGroup');
                $function=array('commit'   => array('name'       => 'addToGroup',
                                                    'parameters' => array($account,'blocked'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been blocked',$key['username'],$key['domain'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='deblock') {
                $this->log_action('removeFromGroup');

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,'blocked'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been de-blocked',$key['username'],$key['domain'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='removefromgroup') {
                if (!strlen($sub_action_parameter)) {
                    printf ("<font color=red>Error: you must enter a group name</font>");
                    break;
                }
                $this->log_action('removeFromGroup');

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,$sub_action_parameter),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been removed from group %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='addtogroup') {
                $this->log_action('addToGroup');
                if (!strlen($sub_action_parameter)) {
                    printf ("<font color=red>Error: you must enter a group name</font>");
                    break;
                }

                $function=array('commit'   => array('name'       => 'addToGroup',
                                                    'parameters' => array($account,$sub_action_parameter),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s is now in group %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='deblock_quota') {
                $this->log_action('removeFromGroup');

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,'quota'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been deblocked from quota',$key['username'],$key['domain'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='disable_pstn') {
                $this->log_action('removeFromGroup');

                $function=array('commit'   => array('name'       => 'removeFromGroup',
                                                    'parameters' => array($account,'free-pstn'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has no access to the PSTN',$key['username'],$key['domain'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='enable_pstn') {
                $this->log_action('addToGroup');

                $function=array('commit'   => array('name'       => 'addToGroup',
                                                    'parameters' => array($account,'free-pstn'),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has access to the PSTN',$key['username'],$key['domain'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='delete') {
                $this->log_action('deleteAccount');
                $function=array('commit'   => array('name'       => 'deleteAccount',
                                                    'parameters' => array($account),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s has been deleted',$key['username'],$key['domain'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action=='prepaid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $result->prepaid=1;
                    $this->log_action('updateAccount');
                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s is now prepaid',$key['username'],$key['domain'])
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='postpaid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);

                    $result->prepaid=0;
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s is now postpaid',$key['username'],$key['domain'])
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='setquota') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->quota         = intval($sub_action_parameter);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has quota set to %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='rmdsfromrpid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties)) $result->properties=array();
                    if (!is_array($result->groups))     $result->groups=array();
                    if (is_numeric($sub_action_parameter) && strlen($result->rpid) > $sub_action_parameter) {
                        printf("%s %s",$result->rpid,$sub_action_parameter);
                        $result->rpid=substr($result->rpid,$sub_action_parameter);
                        printf("%s %s",$result->rpid,$sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric and less than caller if length</font>",$sub_action_parameter);
                        continue;
                    }

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has PSTN caller ID set to %s',$key['username'],$key['domain'],$result->rpid)
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='rpidasusername') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties)) $result->properties=array();
                    if (!is_array($result->groups))     $result->groups=array();
                    if (is_numeric($key['username']))   $result->rpid=$key['username'];

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has PSTN caller ID set to %s',$key['username'],$key['domain'],$key['username'])
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='prefixtorpid') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    if (is_numeric($sub_action_parameter)) {
                        $result->rpid=$sub_action_parameter.$result->rpid;
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has PSTN caller ID set to %s ',$key['username'],$key['domain'],$result->rpid)
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='changecustomer') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    if (is_numeric($sub_action_parameter)) {
                        $result->customer=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has customer set to %s ',$key['username'],$key['domain'],$result->customer)
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='changeowner') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    //print_r($result);
                    // Sanitize data types due to PHP bugs

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    if (is_numeric($sub_action_parameter)) {
                        $result->owner=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s has owner set to %s ',$key['username'],$key['domain'],$result->owner)
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='changefirstname') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->firstName=trim($sub_action_parameter);

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s first name has been set to %s ',$key['username'],$key['domain'],$result->firstName)
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='changelastname') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->lastName=trim($sub_action_parameter);

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('SIP account %s@%s last name has been set to %s ',$key['username'],$key['domain'],$result->lastName)
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='changepassword') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_array($result->properties))   $result->properties=array();
                    if (!is_array($result->groups))       $result->groups=array();
                    $result->password=trim($sub_action_parameter);

                    $result->quota         = intval($result->quota);
                    $result->answerTimeout = intval($result->answerTimeout);
                    $this->log_action('updateAccount');

                    $function=array('commit'   => array('name'       => 'updateAccount',
                                                        'parameters' => array($result),
                                                        'logs'       => array('success' => sprintf('Password for SIP account %s@%s has been changed',$key['username'],$key['domain'])
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action=='addbalance') {
                if (!is_numeric($sub_action_parameter)) {
                    printf ("<font color=red>Error: you must enter a positive balance</font>");
                    break;
                }

                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getAccount');
                $result     = $this->SoapEngine->soapclient->getAccount($account);

                if ((new PEAR)->isError($result)) {
                    $error_msg  = $result->getMessage();
                    $error_fault= $result->getFault();
                    $error_code = $result->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                }

                if (!$result->prepaid) {
                    printf ("<font color=red>Info: SIP account %s@%s is not prepaid, no action performed</font>",$key['username'],$key['domain']);
                    continue;
                }
                $this->log_action('addBalance');
                $function=array('commit'   => array('name'       => 'addBalance',
                                                    'parameters' => array($account,$sub_action_parameter),
                                                    'logs'       => array('success' => sprintf('SIP account %s@%s balance has been increased with %s',$key['username'],$key['domain'],$sub_action_parameter)
                                                                          )
                                                   )

                                );
                $this->SoapEngine->execute($function,$this->html);
            }
        }
        print "</ol>";
    }
}

