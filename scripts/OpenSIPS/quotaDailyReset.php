#!/usr/bin/env php
<?php
/**
 * This script resets the daily quota system
 * This script must be run once at the beginning of each day
 */

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';

// override logger
changeLoggerChannel('quotaDailyReset');

foreach ($DATASOURCES as $k => $v) {
    if (strlen($v["UserQuotaClass"])) {
        unset($CDRS);
        $class_name = $v["class"];
        $CDRS = new $class_name($k);

        $Quota_class = $v["UserQuotaClass"];

        $log=sprintf("Reset daily user quotas for data source %s\n", $v['name']);
        logger($log);

        $Quota = new $Quota_class($CDRS);
        $Quota->resetDailyQuota();
    }
}
