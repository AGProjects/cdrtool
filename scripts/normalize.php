#!/usr/bin/env php
<?php
set_time_limit(0);

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';
require 'rating.php';

// override logger for rating engine
use Monolog\Logger;
use Monolog\Handler\SyslogHandler;
use Monolog\Formatter\LineFormatter;

global $logger;
$logger = new Logger('normalization');
$syslog = new SyslogHandler('cdrtool', 'local0');
$formatter = new LineFormatter("%channel%: %message% %extra%", null, false, true);
$syslog->setFormatter($formatter);
$logger->pushHandler($syslog);

$lockFile = "/var/lock/CDRTool_normalize.lock";

if ($argv[1]) {
    if (preg_match("/^\d{4}\d{2}$/", $argv[1], $m)) {
        $table = 'radacct'.$argv[1];
    } else {
        die("Error: Month must be in YYYYMM format\n");
    }
}

if ($f = fopen($lockFile, "w")) {
    if (flock($f, LOCK_EX + LOCK_NB, $w)) {
        if ($w) {
            criticalAndPrint("Another CDRTool normalization is in progress. Aborting.\n");
            exit(2);
        }
    } else {
        criticalAndPrint("Another CDRTool normalization is in progress. Aborting.\n");
        exit(1);
    }
} else {
    $log = sprintf("Error: Cannot open lock file %s for writing\n", $lockFile);
    criticalAndPrint($log);
    exit(2);
}

foreach ($DATASOURCES as $k => $v) {
    if (strlen($v["normalizedField"] and !$v["skipNormalize"])) {
        $b = time();
        $class_name = $v["class"];

        unset($CDRS);
        $CDRS = new $class_name($k);


        if (is_array($CDRS->db_class)) {
            $db_class = $CDRS->db_class[0];
        } else {
            $db_class = $CDRS->db_class;
        }

        if ($table) $CDRS->table = $table;

        $log = sprintf("Normalize datasource %s, database %s, table %s\n", $k, $db_class, $CDRS->table);
        loggerAndPrint($log);

        $CDRS->NormalizeCDRS();

        $e = time();
        $d = $e - $b;

        if ($CDRS->status['cdr_to_normalize']) {
            $speed = 0;
            if ($d) $speed = number_format($CDRS->status['cdr_to_normalize']/$d, 0, "", "");
            $log = sprintf(
                " %d CDRs, %d normalized in %s s @ %s cps\n",
                $CDRS->status['cdr_to_normalize'],
                $CDRS->status['normalized'],
                $d,
                $speed
            );
            loggerAndPrint($log);
        }

        if (!$table && preg_match("/^(\w+)\d{6}$/", $CDRS->table, $m)) {
            $lastMonthTable=$m[1].date('Ym', mktime(0, 0, 0, date("m")-1, "01", date("Y")));

            $log = sprintf("Normalize datasource %s, database %s, table %s\n", $k, $db_class, $lastMonthTable);
            loggerAndPrint($log);

            $b = time();

            $CDRS->table = $lastMonthTable;
            $CDRS->NormalizeCDRS();

            if ($CDRS->status['cdr_to_normalize']) {
                $e = time();
                $d = $e - $b;

                $speed = 0;
                if ($d) $speed = number_format($CDRS->status['cdr_to_normalize']/$d, 0, "", "");
                $log = sprintf(
                    " %d CDRs, %d normalized in %s s @ %s cps\n",
                    $CDRS->status['cdr_to_normalize'],
                    $CDRS->status['normalized'],
                    $d,
                    $speed
                );
                loggerAndPrint($log);
            }
        }
    }
}

keepAliveRatingEngine();
