#!/usr/bin/php
<?

define_syslog_variables();
openlog("CDRTool trace", LOG_PID, LOG_LOCAL0);

require("/etc/cdrtool/global.inc");
require("cdr_lib.phtml");

$SipTrace = new SIP_trace("sip_trace");

$SipTrace->purgeRecords();

?>
