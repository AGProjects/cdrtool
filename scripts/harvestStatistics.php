#!/usr/bin/php
<?php
require("/etc/cdrtool/global.inc");
require("sip_statistics.php");

$SIPstatistics = new SIPstatistics();
$SIPstatistics->harvestStatistics();

?>
