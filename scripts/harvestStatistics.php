#!/usr/bin/php
<?
require("/etc/cdrtool/global.inc");
require("sip_statistics_lib.phtml");

$errorReporting = (E_ALL & ~(E_NOTICE | E_WARNING));
//$errorReporting = 1; // temporary until warnings are fixed
error_reporting($errorReporting);

$SIPstatistics=new SIPstatistics ();
$SIPstatistics->harvestStatistics();

?>
