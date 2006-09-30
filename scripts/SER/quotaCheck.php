#!/usr/bin/php
<?
# This script blocks accounts in SER
# based on platform wide or user specified quota
# - quota can be specified for cost or IP traffic in table
#   ser.quota_user (see setup/create_quota_user.mysql)
# - account is blocked by adding SIP account in group quota
#   and configuring ser.cfg to reject calls from such users
# - Blocked Users must be deblocked manualy and their quota
#   must be changed to a higher value otherwise
#   subscriber gets blocked again at the next script run
# - Add this script to cron to run every 5 minutes
# - Notifications are sent only once to subscribers
$verbose=1;

$b=time();
define_syslog_variables();
openlog("CDRTool Quota", LOG_PID, LOG_LOCAL0);

$lockFile="/var/lock/CDRTool_QuotaCheck.lock";

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

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

$cdr_source     = "ser_radius";
$CDR_class      = $DATASOURCES[$cdr_source]["class"];
$CDRS           = new $CDR_class($cdr_source);

$SERQuota_class = $DATASOURCES[$cdr_source]["UserQuotaClass"];

if (!$SERQuota_class) $SERQuota_class="SERQuota";
$Quota = new $SERQuota_class($CDRS);

$Quota->checkQuota($DATASOURCES[$cdr_source]['UserQuotaNotify']);

function deleteQuotaCheckLockfile($lockFile) {
	if (!unlink($lockFile)) {
    	print "Error: cannot delete lock file $lockFile. Aborting.\n";
    	syslog(LOG_NOTICE,"Error: cannot delete lock file $lockFile");
	}
}

$e=time();
$d=$e-$b;
if ($d) {
	$log=sprintf("Runtime: %d seconds\n",$d);
	syslog(LOG_NOTICE,"Runtime: $d seconds");
}
?>
