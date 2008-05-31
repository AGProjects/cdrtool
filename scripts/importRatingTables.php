#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../library/rating_lib.phtml");

set_time_limit(0);

$RatingTables= new RatingTables();
$RatingTables->ImportCSVFiles();

if ($RatingTables->mustReload && !reloadRatingEngineTables()) {
    print "Error: cannot connect to network rating engine\n";
}
?>
