#!/usr/bin/php
<?
set_time_limit(0);
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../cdrlib.phtml");

define_syslog_variables();

openlog("CDRTool", LOG_PID, LOG_LOCAL0);
//$verbose=1 ;

$lockFile="/var/lock/CDRTool_normalize.lock";

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
        //printf ("CDR class: %s: %s",$class_name,$v["normalizedField"]);

        unset($CDRS);
        $CDRS = new $class_name($k);
        $total   = 0;
        $success = 0;
        $failure = 0;
        $$speed  = 0;

        $results=$CDRS->NormalizeCDRS();

        if ($results[total])   $total   =$results[total];
        if ($results[success]) $success =$results[success];
        if ($results[failure]) $success =$results[failure];

        $e=time();
        $d=$e-$b;

        if ($d > 0) {
            $speed=number_format($results[total]/$d,0,"","");
            $speed=number_format($total/$d,0,"","");
        }

        if ($total) {
        	print "$k: $total CDRS $success normalized in $d s @ $speed cps\n";
        	syslog(LOG_NOTICE,"$k: $total CDRs, $success normalized in $d s @ $speed cps");
        }
    }
}

?>
