#!/usr/bin/php
<?
# Due to the fact that the monthly usage information is stored in
# a network cache, it is possible to have syncronization problems 
# caused by network connectivity problmes or other causes

# This script can be used to detect differences between database and cached usage

# Warning!
# 
# By running this script the CDR table will be read, 
# which may cause slow database response to other applications


define_syslog_variables();
openlog("CDRTool", LOG_PID, LOG_LOCAL0);

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

while (list($k,$v) = each($DATASOURCES)) {
    if (strlen($v["UserQuotaClass"])) {

        unset($CDRS);
        $class_name=$v["class"];
        $CDRS = new $class_name($k);

        $SERQuota_class = $v["UserQuotaClass"];

		$log=sprintf("Compare user quotas for data source %s\n",$v['name']);
        syslog(LOG_NOTICE,$log);
        //print $log;

        $Quota = new $SERQuota_class($CDRS);
        $Quota->mc_key_accounts = $k.':accounts';
		$Quota->compareUsage();
	}
}
?>
