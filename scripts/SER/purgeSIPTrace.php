#!/usr/bin/php
<?

define_syslog_variables();
openlog("CDRTool purge", LOG_PID, LOG_LOCAL0);

$path=dirname(realpath($_SERVER['PHP_SELF']));
include($path."/../../global.inc");
include($path."/../../cdrlib.phtml");

$SipTrace = new SIP_trace("sip_trace");

$SipTrace->purgeRecords();

?>
