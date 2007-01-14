#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

if (!count($argv) || count($argv) != 2) {
    printf ("Syntax: %s treshhold\n",$_SERVER['PHP_SELF']);
    print "Accounts with quota usage percentage greater than the treshhold will be showed.\n";
    return 0;
}

if (!preg_match("/^\d{1,2}$/",$argv[1])) {
    print "Error: treshold must be an integer between 0 and 100\n";
    return 0;
}

$cdr_source     = "ser_radius";
$CDR_class      = $DATASOURCES[$cdr_source]["class"];
$CDRS           = new $CDR_class($cdr_source);

$SERQuota_class = $DATASOURCES[$cdr_source]["UserQuotaClass"];

if (!$SERQuota_class) $SERQuota_class="SERQuota";

$Quota = new $SERQuota_class($CDRS);


$Quota->showAccountsWithQuota($argv[1]);

?>
