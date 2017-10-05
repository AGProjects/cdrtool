#!/usr/bin/php
<?php
/**
 * Send email to subscribers with the last sessions received in the last 24 hours
 * to notify a susbcriber add him to the missed_calls group
 */

set_time_limit(0);

require '/etc/cdrtool/global.inc';
require 'cdr_generic.php';

while (list($k, $v) = each($DATASOURCES)) {
    if (strlen($v["notifyLastSessions"])) {
        $class_name = $v["class"];

        unset($CDRS);
        $CDRS = new $class_name($k);
        $CDRS->notifyLastSessions(200);
    }
}
