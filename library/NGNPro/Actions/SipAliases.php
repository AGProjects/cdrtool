<?php

class SipAliasesActions extends Actions
{
    var $actions  = array(
        'delete' => 'Delete SIP aliases'
    );

    public function __construct($SoapEngine, $login_credentials)
    {
        parent::__construct($SoapEngine, $login_credentials);
    }

    function execute($selectionKeys, $action, $sub_action_parameter)
    {
        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";
        foreach($selectionKeys as $key) {
            print "<li>";
            flush();

            //printf ("Performing action=%s on key=%s",$action,$key);
            $alias=array('username' => $key['username'],
                         'domain'   => $key['domain']
                        );

            if ($action=='delete') {
                $this->log_action('deleteAlias');

                $function=array('commit'   => array('name'       => 'deleteAlias',
                                                    'parameters' => array($alias),
                                                    'logs'       => array('success' => sprintf('SIP alias %s@%s has been deleted',$key['username'],$key['domain'])
                                                                          )
                                                   )
                                );
                $this->SoapEngine->execute($function,$this->html);
            }
        }
        print "</ol>";

    }
}
