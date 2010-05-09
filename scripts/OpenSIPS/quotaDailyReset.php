#!/usr/bin/php
<?
# this script resets the daily quota system
# this script must be run once at the beginning of each day

require("/etc/cdrtool/global.inc");
require("cdr_generic.php");
require("rating.php");

while (list($k,$v) = each($DATASOURCES)) {
    if (strlen($v["UserQuotaClass"])) {

        unset($CDRS);
        $class_name=$v["class"];
        $CDRS = new $class_name($k);

        $Quota_class = $v["UserQuotaClass"];

		$log=sprintf("Reset daily user quotas for data source %s\n",$v['name']);
        syslog(LOG_NOTICE,$log);

        $Quota = new $Quota_class($CDRS);
		$Quota->resetDailyQuota();
	}
}
?>
