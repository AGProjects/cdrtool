#!/usr/bin/php
<?

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../rating_lib.phtml");

Header("Content-type: text/plain");
Header("Content-Disposition: inline; filename=Rating.txt");
header("Cache-Control: no-cache, must-revalidate");  // HTTP/1.1
header("Pragma: no-cache");

ini_set('max_execution_time','36000');

$RatingTables= new RatingTables();
$RatingTables->ImportCSVFiles();

if ($RatingTables->mustReload && !reloadRatingEngineTables()) {
	print "Error: cannot connect to network rating engine\n";
}
?>
