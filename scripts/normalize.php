#!/usr/bin/php
<?
set_time_limit(0);

require("/etc/cdrtool/global.inc");
require("cdr_generic.php");
require("rating.php");

$lockFile="/var/lock/CDRTool_normalize.lock";

if ($argv[1]) {
	if (preg_match("/^\d{4}\d{2}$/",$argv[1],$m)) {
    	$table='radacct'.$argv[1];
    } else {
    	die ("Error: Month must be in YYYYMM format\n");
    }
}

$f=fopen($lockFile,"w");
if (flock($f, LOCK_EX + LOCK_NB, $w)) {
    if ($w) {
        print "Another CDRTool normalization is in progress. Aborting.\n";
        syslog(LOG_NOTICE,"Another CDRTool normalization is in progress. Aborting.");
        exit(2);
    }
} else {
    print "Another CDRTool normalization is in progress. Aborting.\n";
    syslog(LOG_NOTICE,"Another CDRTool normalization is in progress. Aborting.");
    exit(1);
}

while (list($k,$v) = each($DATASOURCES)) {
    if (strlen($v["normalizedField"])) {
        $b=time();
        $class_name=$v["class"];

        unset($CDRS);
        $CDRS = new $class_name($k);

		if (is_array($CDRS->db_class)) {
        	$db_class=$CDRS->db_class[0];
        } else {
        	$db_class=$CDRS->db_class;
        }

		if ($table) $CDRS->table=$table;

        $log=sprintf("Normalize datasource %s, database %s, table %s\n",$k,$db_class,$CDRS->table);
        print $log;
        syslog(LOG_NOTICE,$log);

        $CDRS->NormalizeCDRS();

        $e=time();
        $d=$e-$b;

        if ($CDRS->status['cdr_to_normalize']) {
            $speed=0;
            if ($d) $speed=number_format($CDRS->status['cdr_to_normalize']/$d,0,"","");
        	$log=sprintf(" %d CDRs, %d normalized in %s s @ %s cps\n",$CDRS->status['cdr_to_normalize'],$CDRS->status['normalized'],$d,$speed);
            print $log;
        	syslog(LOG_NOTICE,$log);
        }

        if (!$table && preg_match("/^(\w+)\d{6}$/",$CDRS->table,$m)) {
        	$lastMonthTable=$m[1].date('Ym', mktime(0, 0, 0, date("m")-1, "01", date("Y")));

            $log=sprintf("Normalize datasource %s, database %s, table %s\n",$k,$db_class,$lastMonthTable);
            print $log;
            syslog(LOG_NOTICE,$log);

	        $b=time();

			$CDRS->table=$lastMonthTable;
            $CDRS->NormalizeCDRS();

            if ($CDRS->status['cdr_to_normalize']) {
                $e=time();
                $d=$e-$b;

                $speed=0;
                if ($d) $speed=number_format($CDRS->status['cdr_to_normalize']/$d,0,"","");
                $log=sprintf(" %d CDRs, %d normalized in %s s @ %s cps\n",$CDRS->status['cdr_to_normalize'],$CDRS->status['normalized'],$d,$speed);
                print $log;
                syslog(LOG_NOTICE,$log);
            }
        }
    }
}

keepAliveRatingEngine();

?>
