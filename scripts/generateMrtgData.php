#!/usr/bin/php
<?
require("/etc/cdrtool/global.inc");
require("sip_statistics.php");

if (count($argv)!= 3 ) {
    printf ("Usage: $PHP_SELF domain dataType\n");
    exit ;
}

$SIPstatistics=new SIPstatistics ();
$SIPstatistics->generateMrtgData($argv[1],$argv[2]);

?>
