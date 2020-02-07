#!/usr/bin/env php
<?php
require("/etc/cdrtool/global.inc");
require('cdr_generic.php');
require("rating.php");

set_time_limit(0);

$lockFile = sprintf("/var/lock/CDRTool_import_rates.lock");
$abort_text = "Another import operation is in progress. Try again later.\n";

$f = fopen($lockFile, "w");
if (flock($f, LOCK_EX + LOCK_NB, $w)) {
    if ($w) {
        print $abort_text;
        syslog(LOG_NOTICE, $abort_text);
        exit(2);
    }
} else {
    print $abort_text;
    syslog(LOG_NOTICE, $abort_text);
    exit(1);
}

$RatingTables = new RatingTables();
$RatingTables->ImportCSVFiles();

if ($RatingTables->mustReload) {
    if (!reloadRatingEngineTables()) {
        print "Error: cannot connect to network rating engine\n";
    }
}
