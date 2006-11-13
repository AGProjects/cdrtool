#!/usr/bin/php
<?
# Due to the fact that the monthly usage information is stored in
# a network cache, it is possible to have syncronization problems 
# caused by network connectivity problmes or other causes

# This script can be used to detect differences between real 
# CDR usage and cached usage

# Warning!
# 
# By running this script the CDR table will be read, 
# which may cause slow database response to other applications


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
$Quota->compareUsage();

?>
