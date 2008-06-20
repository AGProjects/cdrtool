#!/usr/bin/php
<?
# This script blocks accounts in OpenSER
# based on platform wide or user specified quota
# - quota can be specified in openser.subscriber.quota
# - account is blocked by adding SIP account in group quota
#   and configuring openser.cfg to reject calls from such users
# - Blocked Users must be deblocked manualy and their quota
#   must be changed to a higher value otherwise
#   subscriber gets blocked again at the next script run
# - Add this script to cron to run every 5 minutes
# - Notifications are sent once to subscribers
#$verbose=1;
set_time_limit(0);

require("/etc/cdrtool/global.inc");
require("cdr_lib.phtml");

define_syslog_variables();
openlog("CDRTool quota", LOG_PID, LOG_LOCAL0);

$b=time();

$lockFile=sprintf("/var/lock/CDRTool_QuotaCheck.lock",$cdr_source);

$abort_text="Another check is in progress. Try again later.\n";

$f=fopen($lockFile,"w");
if (flock($f, LOCK_EX + LOCK_NB, $w)) {
    if ($w) {
        print $abort_text;
        syslog(LOG_NOTICE,$abort_text);
        exit(2);
    }
} else {
    print $abort_text;
    syslog(LOG_NOTICE,$abort_text);
    exit(1);
}

while (list($k,$v) = each($DATASOURCES)) {
    if (strlen($v["UserQuotaClass"])) {

        unset($CDRS);
        $class_name=$v["class"];
        $CDRS = new $class_name($k);

        $SERQuota_class = $v["UserQuotaClass"];

		$log=sprintf("Checking user quotas for data source %s\n",$v['name']);
        syslog(LOG_NOTICE,$log);
        //print $log;

        $Quota = new $SERQuota_class($CDRS);
        $Quota->checkQuota($v['UserQuotaNotify']);
        $d=time()-$b;
        $log=sprintf("Runtime: %d s",$d);
        syslog(LOG_NOTICE,$log);
	}
}

function deleteQuotaCheckLockfile($lockFile) {
	if (!unlink($lockFile)) {
    	print "Error: cannot delete lock file $lockFile. Aborting.\n";
    	syslog(LOG_NOTICE,"Error: cannot delete lock file $lockFile");
	}
}

