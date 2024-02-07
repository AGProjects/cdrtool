<?php

class CustomersActions extends Actions {
    var $actions=array(
	    'delete'         => 'Delete customers'
    );

    public function __construct($SoapEngine, $login_credentials)
    {
        parent::__construct($SoapEngine, $login_credentials);
    }

    function execute($selectionKeys,$action,$sub_action_parameter) {

        if (!in_array($action,array_keys($this->actions))) {
            print "<font color=red>Error: Invalid action $action</font>";
            return false;
        }

        print "<ol>";

        foreach($selectionKeys as $key) {
            flush();
            print "<li>";

            if ($action=='delete') {
                $this->log_action('deleteAccount');
                $function=array('commit'  => array('name'       => 'deleteAccount',
                                                    'parameters' => array(intval($key)),
                                                   'logs'       => array('success' => sprintf('Customer id %s has been deleted',$key)))
                                );
                $this->SoapEngine->execute($function,$this->html);
            }
        }

        print "</ol>";
    }
}

