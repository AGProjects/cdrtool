#!/usr/bin/env php
<?php
/**
 * This script resets the quota system
 * This script must be run once at the beginning of the calendar month
 */

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';

// override logger
changeLoggerChannel('quotaReset');

foreach ($DATASOURCES as $k => $v) {
    if (strlen($v["UserQuotaClass"])) {
        unset($CDRS);
        $class_name = $v["class"];
        $CDRS = new $class_name($k);

        $Quota_class = $v["UserQuotaClass"];

        $log = sprintf("Reset user quotas for data source %s\n", $v['name']);
        logger($log);
        //print $log;

        $Quota = new $Quota_class($CDRS);
        $Quota->deleteQuotaInitFlag();
    }
}
