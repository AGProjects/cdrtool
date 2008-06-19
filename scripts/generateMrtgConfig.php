#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
require($path."/../global.inc");
require("sip_statistics_lib.phtml");

$SIPstatistics=new SIPstatistics ();
$SIPstatistics->generateMrtgConfigFile();

?>
