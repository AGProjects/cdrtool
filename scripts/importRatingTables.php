#!/usr/bin/php
<?
require("/etc/cdrtool/global.inc");
require("rating.php");

set_time_limit(0);

$RatingTables= new RatingTables();
$RatingTables->ImportCSVFiles();

if ($RatingTables->mustReload && !reloadRatingEngineTables()) {
    print "Error: cannot connect to network rating engine\n";
}
?>
