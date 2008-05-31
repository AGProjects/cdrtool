#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../library/sip_statistics_lib.phtml");

$SIPstatistics=new SIPstatistics ();
$SIPstatistics->generateMrtgConfigFile();

?>
