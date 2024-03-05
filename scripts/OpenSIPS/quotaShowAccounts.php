#!/usr/bin/env php
<?php
require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';

if (!count($argv) || count($argv) != 2) {
    printf("Syntax: %s treshhold\n", $_SERVER['PHP_SELF']);
    print "Accounts with quota usage percentage greater than the treshhold will be showed.\n";
    return 0;
}

if (!preg_match("/^\d{1,2}$/", $argv[1])) {
    print "Error: treshold must be an integer between 0 and 100\n";
    return 0;
}

foreach ($DATASOURCES as $k => $v) {
    if (strlen($v["UserQuotaClass"])) {
        unset($CDRS);
        $class_name = $v["class"];
        $CDRS = new $class_name($k);

        $Quota_class = $v["UserQuotaClass"];

        $log=sprintf("Checking user quotas for data source %s\n", $v['name']);
        syslog(LOG_NOTICE, $log);
        //print $log;

        $Quota = new $Quota_class($CDRS);
        $Quota->showAccountsWithQuota($argv[1]);
    }
}
