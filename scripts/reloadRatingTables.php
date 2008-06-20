#!/usr/bin/php
<?
require("/etc/cdrtool/global.inc");
require("rating.php");

if (!reloadRatingEngineTables()) {
	die("Error: Cannot connect to network Rating Engine $errstr ($errno). \n");
} else {
    printf ("Rating engine at %s:%s reloaded. See syslog for more information. \n",$RatingEngine["socketIP"],$RatingEngine["socketPort"]);
}
?>
