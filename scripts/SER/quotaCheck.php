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

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

define_syslog_variables();
openlog("CDRTool quota", LOG_PID, LOG_LOCAL0);

$b=time();

$lockFile=sprintf("/var/lock/CDRTool_QuotaCheck.lock",$cdr_source);

$f=fopen($lockFile,"w");
if (flock($f, LOCK_EX + LOCK_NB, $w)) {
    if ($w) {
        print "Another CDRTool quota check is in progress. Aborting.\n";
        syslog(LOG_NOTICE,"Another CDRTool quota check is in progress. Aborting.");
        exit(2);
    }
} else {
    print "Another CDRTool quota check is in progress. Aborting.\n";
    syslog(LOG_NOTICE,"Another CDRTool quota check is in progress. Aborting.");
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
        $Quota->mc_key_accounts = $k.':accounts';
        $Quota->checkQuota($v['UserQuotaNotify']);
	}
}

function deleteQuotaCheckLockfile($lockFile) {
	if (!unlink($lockFile)) {
    	print "Error: cannot delete lock file $lockFile. Aborting.\n";
    	syslog(LOG_NOTICE,"Error: cannot delete lock file $lockFile");
	}
}

$e=time();
$d=$e-$b;
if ($d > 5) {
	$log=sprintf("Runtime: %d s",$d);
	syslog(LOG_NOTICE,$log);
}
?>
