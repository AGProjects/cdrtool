#!/usr/bin/php
<?
# Note:
# This script has not been tested anymore since the auto-rotation performed by
# the MySQL stored procedures has been introduced in version 5.0

define_syslog_variables();
openlog("cdrtool purge", LOG_PID, LOG_LOCAL0);

require("/etc/cdrtool/global.inc");
require("cdr_generic.php");

while (list($k,$v) = each($DATASOURCES)) {
    if (strlen($v['purgeCDRsAfter'])) {
        $class_name=$v["class"];

        $log=sprintf("Datasource: %s\n",$v['name']);
        print $log;
        syslog(LOG_NOTICE,$log);

        unset($CDRS);
        $CDRS = new $class_name($k);

        if ($argv[1] && !preg_match("/^(\d{4})(\d{2})$/",$argv[1],$m)) {
        	print "Error: Month must be in YYYYMM format\n";
            continue;
        } else {
        	$endDate=date('Y-m-d', time() - 3600*24*$v['purgeCDRsAfter']);
        	$log=sprintf("Purge CDRs before %s\n",$endDate);
        	print $log;
        	syslog(LOG_NOTICE,$log);
        }

        $CDRS->purgeTable($argv[1]);
        print "\n";
    }
}

?>
