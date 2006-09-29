#!/usr/bin/php
<?
# this script deblocks all SIP subscriber blocked by the
# quota system and removes any previous stored usage
# information for the curent calendar month

define_syslog_variables();
openlog("CDRTool", LOG_PID, LOG_LOCAL0);

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

$cdr_source     = "ser_radius";
$CDR_class      = $DATASOURCES[$cdr_source]["class"];
$CDRS           = new $CDR_class($cdr_source);

$SERQuota_class = $DATASOURCES[$cdr_source]["UserQuotaClass"];
if (!$SERQuota_class) $SERQuota_class="SERQuota";

$Quota = new $SERQuota_class($CDRS);
$Quota->ResetUserQuotas();
$CDRS->deleteMonthlyUsage();

if (is_object($CDRS->mc)) $CDRS->mc->delete('quotaCheckInit');

?>
