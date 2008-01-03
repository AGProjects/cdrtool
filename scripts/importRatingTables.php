#!/usr/bin/php
<?

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../rating_lib.phtml");

Header("Content-type: text/plain");
Header("Content-Disposition: inline; filename=Rating.txt");
header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");

$RatingTables= new RatingTables();
if ($RatingTables->ImportCSVFiles()) {
    if (!reloadRatingEngineTables()) {
        print "Error: Cannot connect to network Rating Engine $errstr ($errno). ";
    }
}
?>
