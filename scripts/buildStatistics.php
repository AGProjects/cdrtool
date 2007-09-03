#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../sip_statistics_lib.phtml");

$errorReporting = (E_ALL & ~(E_NOTICE | E_WARNING));
//$errorReporting = 1; // temporary until warnings are fixed
error_reporting($errorReporting);

$SIPstatistics=new SIPstatistics ();
$SIPstatistics->buildStatistics();

?>
