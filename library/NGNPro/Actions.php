<?php

class Actions
{
    // this class perfom actions on an array of entities returned by selections

    public $actions = array();
    public $version = 1;
    public $sub_action_parameter_size = 35;
    public $html = true;

    public $SoapEngine;
    private $login_credentials;
    private $adminonly;

    public function __construct($SoapEngine, $login_credentials)
    {
        $this->SoapEngine = $SoapEngine;
        $this->login_credentials = $login_credentials;
        $this->version    = $this->SoapEngine->version;
        $this->adminonly  = $this->SoapEngine->adminonly;
    }

    protected function log_action($action = 'Unknown')
    {
        global $CDRTool;
        $location = "Unknown";
        $_loc = geoip_record_by_name($_SERVER['REMOTE_ADDR']);
        if ($_loc['country_name']) {
            $location = $_loc['country_name'];
        }
        $log = sprintf(
            "CDRTool login username=%s, type=%s, impersonate=%s, IP=%s, location=%s, action=%s:%s, script=%s",
            $this->login_credentials['username'],
            $this->login_credentials['login_type'],
            $CDRTool['impersonate'],
            $_SERVER['REMOTE_ADDR'],
            $location,
            $this->SoapEngine->port,
            $action,
            $_SERVER['PHP_SELF']
        );
        logger($log);
    }

    protected function checkLogSoapError($result, $syslog = false)
    {
        if (!(new PEAR)->isError($result)) {
            return false;
        }
        $error_msg  = $result->getMessage();
        $error_fault= $result->getFault();
        $error_code = $result->getCode();
        if ($syslog) {
            $log = sprintf(
                "SOAP request error from %s: %s (%s): %s",
                $this->SoapEngine->SOAPurl,
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
            logger($log);
        } else {
            printf(
                "<font color=red>Error: %s (%s): %s</font>",
                $error_msg,
                $error_fault->detail->exception->errorcode,
                $error_fault->detail->exception->errorstring
            );
        }
        return true;
    }

    public function execute($selectionKeys, $action, $sub_action_parameter)
    {
    }

    protected function showActionsForm($filters, $sorting, $hideParameter = false)
    {
        if (!count($this->actions)) return;

        printf(
            "<form class=form-inline method=post name=actionform action=%s>",
            $_SERVER['PHP_SELF']
        );
        print "
        <div class='well well-small'>
        ";

        print "
        <input class='btn btn-warning' type=submit value='Perform this action on the selection:'>
        <input type=hidden name=action value=PerformActions>
        ";
        if ($this->adminonly) {
            print "
            <input type=hidden name=adminonly value=$this->adminonly>
            ";
        }


        print "<select name=sub_action>";
        $j=0;
        foreach (array_keys($this->actions) as $_action) {
            $j++;
            printf(
                "<option value='%s'>%d. %s",
                $_action,
                $j,
                $this->actions[$_action]
            );
        }
        print "</select>";

        if (!$hideParameter) {
            printf(
                "<input type=text class=span2 size=%d name=sub_action_parameter>",
                $this->sub_action_parameter_size
            );
        }
        print "
        <p class=pull-right>
        ";
        print " Maximum of 500 records
        </p>
        ";

        foreach (array_keys($filters) as $_filter) {
            printf(
                "<input type=hidden name='%s_filter' value='%s'>\n",
                $_filter,
                $filters[$_filter]
            );
        }

        foreach (array_keys($sorting) as $_sorting) {
            printf(
                "<input type=hidden name='%s' value='%s'>\n",
                $_sorting,
                $sorting[$_sorting]
            );
        }

        printf("<input type=hidden name=service value='%s'>", $this->SoapEngine->service);

        foreach (array_keys($this->SoapEngine->extraFormElements) as $element) {
            if (!strlen($this->SoapEngine->extraFormElements[$element])) continue;
            printf("<input type=hidden name=%s value='%s'>\n", $element, $this->SoapEngine->extraFormElements[$element]);
        }
        print "</div>
            </form>
        ";
    }
}
