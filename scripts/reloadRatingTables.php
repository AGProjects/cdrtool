#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../library/rating_lib.phtml");

if (!reloadRatingEngineTables()) {
	die("Error: Cannot connect to network Rating Engine $errstr ($errno). \n");
} else {
    printf ("Rating engine at %s:%s reloaded. See syslog for more information. \n",$RatingEngine["socketIP"],$RatingEngine["socketPort"]);
}
?>
