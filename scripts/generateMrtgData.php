#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../global.inc");
include($path."/../sip_statistics_lib.phtml");

if (count($argv)!= 3 ) {
    printf ("Usage: $PHP_SELF domain dataType\n");
    exit ;
}

$SIPstatistics=new SIPstatistics ();
$SIPstatistics->generateMrtgData($argv[1],$argv[2]);

?>
