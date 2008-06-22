#!/usr/bin/php
<?

# this script creates monthly tables radacctYYYYMM
# and copies CDR data from radacct table into it 

# this script must run daily during a window of low traffic

# Note:
# This script has not been tested anymore since the auto-rotation performed by
# the MySQL stored procedures has been introduced in version 5.0


define_syslog_variables();
openlog("CDRTool autorotate", LOG_PID, LOG_LOCAL0);

require("/etc/cdrtool/global.inc");
require("cdr_generic.php");
require("rating.php");

$cdr_source     = "ser_radius";
$CDR_class      = $DATASOURCES[$cdr_source]["class"];
$CDRS           = new $CDR_class($cdr_source);

$CDRS->rotateTable($argv[1],$argv[2],$argv[3]);

?>
