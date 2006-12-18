#!/usr/bin/php
<?
$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

$cdr_source     = "ser_radius";
$CDR_class      = $DATASOURCES[$cdr_source]["class"];
$CDRS           = new $CDR_class($cdr_source);

$SERQuota_class = $DATASOURCES[$cdr_source]["UserQuotaClass"];

if (!$SERQuota_class) $SERQuota_class="SERQuota";

$Quota = new $SERQuota_class($CDRS);

$Quota->showAccountsWithQuota();

?>
