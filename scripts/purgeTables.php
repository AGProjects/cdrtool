#!/usr/bin/php
<?

require("/etc/cdrtool/global.inc");
require("cdr_generic.php");

// purge old logs of debit balance
$PrepaidHistory = new PrepaidHistory();
$PrepaidHistory->purge();

print "\n";

// purge old CDRs when using a central radius table
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
