#!/usr/bin/php
<?
# This script inits the memcache server with
# the curent calendar month usage of each SIP subscriber
#
# It is necessary to run this script only if the
# memcache server has ben restarted
#
# the usage is saved in memcache server with the key:
#		mu:user@domain
# the value saved per key has the format:
#      calls=xxx cost=xxx duration=xxx traffic=xxx
#
# The current usage is updated by the normalization process
#

$verbose=1;

$b=time();
define_syslog_variables();
openlog("CDRTool Quota", LOG_PID, LOG_LOCAL0);

$lockFile="/var/lock/CDRTool_QuotaInit.lock";

$f=fopen($lockFile,"w");
if (flock($f, LOCK_EX + LOCK_NB, $w)) {
    if ($w) {
        print "Another CDRTool quota init is in progress. Aborting.\n";
        syslog(LOG_NOTICE,"Another CDRTool quota check is in progress. Aborting.");
        exit(2);
    }
} else {
    print "Another CDRTool quota init is in progress. Aborting.\n";
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
$Quota->ResetUserQuotas();
$CDRS->deleteMonthlyUsage();
$Quota->getDatabaseUsage();

function deleteQuotaCheckLockfile($lockFile) {
	if (!unlink($lockFile)) {
    	print "Error: cannot delete lock file $lockFile. Aborting.\n";
    	syslog(LOG_NOTICE,"Error: cannot delete lock file $lockFile");
	}
}

$e=time();
$d=$e-$b;
$log=sprintf("Runtime: %d seconds\n",$d);
syslog(LOG_NOTICE,"Runtime: $d seconds");
print $log;

?>
