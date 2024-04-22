#!/usr/bin/env php
<?php
/**
 * This script blocks accounts in OpenSIPS
 * based on platform wide or user specified quota
 * - quota can be specified in opensips.subscriber.quota
 * - account is blocked by adding SIP account in group quota
 *   and configuring opensips.cfg to reject calls from such users
 * - Blocked Users must be deblocked manualy and their quota
 *   must be changed to a higher value otherwise
 *   subscriber gets blocked again at the next script run
 * - Add this script to cron to run every 5 minutes
 * - Notifications are sent once to subscribers
 */

//$verbose=1;
set_time_limit(0);

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';


// override logger
changeLoggerChannel('quotaCheck');

$b = time();

$lockFile = sprintf("/var/lock/CDRTool_QuotaCheck.lock");

$abort_text = "Another check is in progress. Try again later.\n";

$f = fopen($lockFile, "w");
if (flock($f, LOCK_EX + LOCK_NB, $w)) {
    if ($w) {
        criticalAndPrint($abort_text);
        exit(2);
    }
} else {
    criticalAndPrint($abort_text);
    exit(1);
}

foreach ($DATASOURCES as $k => $v) {
    if (isset($v["UserQuotaClass"]) && strlen($v["UserQuotaClass"])) {
        unset($CDRS);
        $class_name = $v["class"];
        $CDRS = new $class_name($k);

        $Quota_class = $v["UserQuotaClass"];

        $log=sprintf("Checking user quotas for data source %s\n", $v['name']);
        logger($log);
        //print $log;

        $Quota = new $Quota_class($CDRS);
        $Quota->checkQuota($v['UserQuotaNotify']);
        $d = time() - $b;
        if ($d > 5) {
            $log = sprintf("Runtime: %d s", $d);
            logger($log);
        }
    }
}

function deleteQuotaCheckLockfile($lockFile)
{
    if (!unlink($lockFile)) {
        errorAndPrint("Error: cannot delete lock file $lockFile");
    }
}
