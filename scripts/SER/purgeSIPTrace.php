#!/usr/bin/php
<?

define_syslog_variables();
openlog("cdrtool", LOG_PID, LOG_LOCAL0);

require("/etc/cdrtool/global.inc");
require("cdr_generic.php");

$SipTrace = new SIP_trace("sip_trace");

$SipTrace->purgeRecords();

?>
