#!/usr/bin/php
<?
require("/etc/cdrtool/global.inc");
require("sip_statistics_lib.phtml");

$SIPstatistics=new SIPstatistics ();
$SIPstatistics->generateMrtgConfigFile();

?>
