<?php

class EnumMappingsActions extends Actions {
    var $actions=array(
                       'changettl'      => 'Change TTL to:',
                       'changeowner'    => 'Change owner to:',
                       'changeinfo'     => 'Change info to:',
                       'delete'         => 'Delete ENUM mappings'
                       );

    var $mapping_fields=array('id'       => 'integer',
                              'type'     => 'text',
                              'mapto'    => 'text',
                              'priority' => 'integer',
                              'ttl'      => 'integer'
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

            $enum_id=array('number' => $key['number'],
                           'tld'    => $key['tld']
                          );
            if ($action=='delete') {

                //printf ("Performing action=%s on key=%s",$action,$key);
                $function=array('commit'   => array('name'       => 'deleteNumber',
                                                    'parameters' => array($enum_id),
                                                    'logs'       => array('success' => sprintf('ENUM number +%s under %s has been deleted',$key['number'],$key['tld'])
                                                                          )
                                                   )

                                );

                $this->SoapEngine->execute($function,$this->html);
            } else if ($action  == 'changettl') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getNumber');
                $number     = $this->SoapEngine->soapclient->getNumber($enum_id);

                if ((new PEAR)->isError($number)) {
                    $error_msg  = $number->getMessage();
                    $error_fault= $number->getFault();
                    $error_code = $number->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    if (!is_numeric($sub_action_parameter)) {
                        printf ("<font color=red>Error: TTL '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }

                    $new_mappings=array();
                    foreach ($number->mappings as $_mapping) {
                        foreach (array_keys($this->mapping_fields) as $field) {
                            if ($field == 'ttl') {
                                $new_mapping[$field]=intval($sub_action_parameter);
                            } else {
                                if ($this->mapping_fields[$field] == 'integer') {
                                    $new_mapping[$field]=intval($_mapping->$field);
                                } else {
                                    $new_mapping[$field]=$_mapping->$field;
                                }
                            }
                        }

                        $new_mappings[]=$new_mapping;
                    }

                    $number->mappings=$new_mappings;
                    $this->log_action('updateNumber');

                    $function=array('commit'   => array('name'       => 'updateNumber',
                                                        'parameters' => array($number),
                                                        'logs'       => array('success' => sprintf('ENUM number %s@%s TTL has been set to %d',$key['number'],$key['tld'],intval($sub_action_parameter))
                                                                              )
                                                       )

                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeowner') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getNumber');
                $number     = $this->SoapEngine->soapclient->getNumber($enum_id);

                if ((new PEAR)->isError($number)) {
                    $error_msg  = $number->getMessage();
                    $error_fault= $number->getFault();
                    $error_code = $number->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    $new_mappings=array();
                    foreach ($number->mappings as $_mapping) {
                        $new_mappings[]=$_mapping;
                    }

                    $number->mappings=$new_mappings;

                    if (is_numeric($sub_action_parameter)) {
                        $number->owner=intval($sub_action_parameter);
                    } else {
                        printf ("<font color=red>Error: Owner '%s' must be numeric</font>",$sub_action_parameter);
                        continue;
                    }
                    $this->log_action('updateNumber');

                    $function=array('commit'   => array('name'       => 'updateNumber',
                                                        'parameters' => array($number),
                                                        'logs'       => array('success' => sprintf('ENUM number %s@%s owner has been set to  %d',$key['number'],$key['tld'],intval($sub_action_parameter))
                                                                              )
                                                       )
                                    );
                    $this->SoapEngine->execute($function,$this->html);
                }
            } else if ($action  == 'changeinfo') {
                $this->SoapEngine->soapclient->addHeader($this->SoapEngine->SoapAuth);
                $this->log_action('getNumber');
                $number     = $this->SoapEngine->soapclient->getNumber($enum_id);

                if ((new PEAR)->isError($number)) {
                    $error_msg  = $number->getMessage();
                    $error_fault= $number->getFault();
                    $error_code = $number->getCode();
                    printf ("<font color=red>Error: %s (%s): %s</font>",$error_msg, $error_fault->detail->exception->errorcode,$error_fault->detail->exception->errorstring);
                    break;
                } else {
                    $new_mappings=array();
                    foreach ($number->mappings as $_mapping) {
                        $new_mappings[]=$_mapping;
                    }

                    $number->mappings=$new_mappings;

                    $number->info=trim($sub_action_parameter);
                    $this->log_action('updateNumber');

                    $function=array('commit'   => array('name'       => 'updateNumber',
                                                        'parameters' => array($number),
                                                        'logs'       => array('success' => sprintf('ENUM number %s@%s info has been set to %s',$key['number'],$key['tld'],trim($sub_action_parameter))
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
