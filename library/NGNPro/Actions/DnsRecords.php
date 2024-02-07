<?php

class DnsRecordsActions extends Actions {
    var $sub_action_parameter_size = 50;

    var $actions = array(
        'changettl'      => 'Change TTL to:',
        'changepriority' => 'Change Priority to:',
        'changevalue'    => 'Change value to:',
        'delete'         => 'Delete records'
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
            print "<li>";
            //printf ("Performing action=%s on key=%s",$action,$key['id']);

            if ($action=='delete') {
                $this->log_action('deleteRecord');
                $function=array('commit'   => array('name'       => 'deleteRecord',
                                                    'parameters' => array(intval($key['id'])),
                                                    'logs'       => array('success' => sprintf('Record %d has been deleted',$key['id'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action  == 'changettl') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getRecord');
                $record     = $this->SoapEngine->soapclient->getRecord($key['id']);

                if ((new PEAR)->isError($record)) {
                    $error_msg  = $record->getMessage();
                    $error_fault= $record->getFault();
                    $error_code = $record->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {

                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: TTL '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $record->ttl=intval($sub_action_parameter);
                    $this->log_action('updateRecord');

                    $function=array('commit'   => array('name'       => 'updateRecord',
                                                        'parameters' => array($record),
                                                        'logs'       => array('success' => sprintf('TTL for record %d has been set to %d',$key['id'],intval($sub_action_parameter))
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changepriority') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getRecord');
                $record     = $this->SoapEngine->soapclient->getRecord($key['id']);

                if ((new PEAR)->isError($record)) {
                    $error_msg  = $record->getMessage();
                    $error_fault= $record->getFault();
                    $error_code = $record->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    if (is_numeric($sub_action_parameter)) {
                        $record->priority=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: Priority '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $this->log_action('updateRecord');

                    $function=array('commit'   => array('name'       => 'updateRecord',
                                                        'parameters' => array($record),
                                                        'logs'       => array('success' => sprintf('Priority for record %d has been set to %d',$key['id'],intval($sub_action_parameter))
                                                                              )
                                                       )
                                    );

                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changevalue') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getRecord');
                $record     = $this->SoapEngine->soapclient->getRecord($key['id']);

                if ((new PEAR)->isError($record)) {
                    $error_msg  = $record->getMessage();
                    $error_fault= $record->getFault();
                    $error_code = $record->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                	$record->value=$sub_action_parameter;
                    $this->log_action('updateRecord');

                    $function=array('commit'   => array('name'       => 'updateRecord',
                                                        'parameters' => array($record),
                                                        'logs'       => array('success' => sprintf('Value of record %d has been set to %s',$key['id'],$sub_action_parameter)
                                                                              )
                                                       )
                                    );

                    $this->SoapEngine->execute($function,$this->html);
                }
            }
        }

        print "</ol>";
    }
}

